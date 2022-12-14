<?php

namespace App\Http\Controllers\Subscription;

use App\Actions\TenantPlanChangeAction;
use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsurePlanCanBeChanged;
use App\Http\Requests\CheckoutRequest;
use App\Models\CreditCard;
use App\Models\Receipt;
use App\Models\Tenant;
use App\Repositories\PlanRepository;
use Illuminate\Http\Request;
use Jojostx\Larasubs\Models\Plan;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class CheckoutController extends Controller
{
    /**
     * The TenantPlanChangeAction instance.
     */
    protected TenantPlanChangeAction $tenantPlanChangeAction;

    /**
     * The plan repository instance.
     */
    protected PlanRepository $planRepository;

    public function __construct()
    {
        $this->middleware(EnsurePlanCanBeChanged::class);
    }

    protected function planRepository(): PlanRepository
    {
        return $this->planRepository ??= app(PlanRepository::class);
    }

    protected function tenantPlanChangeAction(): TenantPlanChangeAction
    {
        return $this->tenantPlanChangeAction ??= app(TenantPlanChangeAction::class);
    }

    public function index(Request $request)
    {
        // exclude free plans
        $plans = $this->planRepository()->getActive();
        $selectedPlan = $this->planRepository()->getActiveBySlug($request->get('plan', ''));
        $tenant = \tenant();
        $billingInfo = $tenant->billingInfo;

        return \view('subscriptions.checkout', compact('plans', 'selectedPlan', 'tenant', 'billingInfo'));
    }

    public function create(CheckoutRequest $request,)
    {
        $validated = $request->validated();
        $plan = $this->planRepository()->getActiveBySlug($validated['plan']);

        if ($plan->isFree()) {
            // perform plan downgrade and redirect to dashboard
            return $this->handleSubscriptionPlanChange($plan);
        }

        return $this->initiatePlanCheckoutRequest($plan, $validated);
    }

    public function update(Request $request)
    {
        $status = $request->status;

        //if payment is successful
        if ($status == 'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();

            $response = Flutterwave::verifyTransaction($transactionID);

            $data = $response['data'];

            $plan = $this->planRepository()->getActiveBySlug($data['meta']['plan']);

            if (
                isset($plan) &&
                $data['status'] == "successful"
                && $data['amount'] >= getPlanPrice($plan)
                && $data['currency'] == $plan->currency
            ) {
                // Success! Confirm the customer's payment
                $tenant = getTenant($data['meta']['tenant']);

                if (
                    $tenant->is(tenant()) &&
                    $this->createReceipt($tenant, $data['meta']) &&
                    $this->updatePaymentMethod($tenant, $data['card'])
                ) {
                    return $this->handleSubscriptionPlanChange($plan);
                }
            } else {
                return \back()->with('checkout_error', 'Unable to complete checkout');
            }
        } else {
            return \back()->with('checkout_error', $request['message']);
        }

        return 'updated';
    }

    protected function initiatePlanCheckoutRequest(Plan $plan, array $data)
    {
        $tenant = tenant();

        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'tx_ref' => $reference,
            'payment_options' => 'card',
            'amount' => \getPlanPrice($plan),
            'currency' => $plan->currency,
            'redirect_url' => route('filament.plans.checkout.update'),
            'customer' => [
                "name" => $data['name'],
                'email' => $tenant->email,
                'phone_number' => auth()->user()->phone_number
            ],
            "meta" => [
                'tenant' => $tenant->id,
                'plan' => $data['plan'],
                'organization' => $data['organization'],
                "tax_number" => $data['tax_number'],
                "address" => $data['address'],
                "zip_code" => $data['zip_code'],
            ],
            "customizations" => [
                "title" => 'Plan Checkout',
                "description" => "Payment for {$plan->name} plan",
            ]
        ];

        $payment = Flutterwave::initializePayment($data);

        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return \back()->with('checkout_error', $payment['message']);
        }

        return redirect()->to($payment['data']['link']);
    }

    protected function handleSubscriptionPlanChange(Plan $plan)
    {
        $tenant = tenant();

        // perform plan downgrade and redirect to dashboard
        $subscription = $this->tenantPlanChangeAction()->handle($tenant, $plan);

        if (blank($subscription)) {
            return back()->with('checkout_error', 'Unable to complete plan checkout');
        }

        return \redirect()->route('filament.pages.settings')->with('checkout_success', 'Plan checkout successfully');
    }

    protected function updatePaymentMethod(Tenant $tenant, array $data): CreditCard
    {
        return $tenant->createCreditCard(
            [
                "token" => $data['token'],
                "first_6" => $data['first_6digits'],
                "last_4" => $data['last_4digits'],
                "issuer" => $data['issuer'],
                "country" => $data['country'],
                "type" => $data['type'],
                "expiry" => $data['expiry'],
            ]
        );
    }

    protected function createReceipt(Tenant $tenant, array $data): Receipt
    {
        return $tenant->createReceipt([
            'amount' => $data['amount'],
            'organization' => $data['organization'],
            'name' => $data['name'],
            'email' => $data['email'],
            'tax_number' => $data['tax_number'],
            'address' => $data['address'],
            'zip_code' => $data['zip_code'],
        ]);
    }
}
