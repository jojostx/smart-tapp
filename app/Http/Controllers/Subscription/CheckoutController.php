<?php

namespace App\Http\Controllers\Subscription;

use App\Actions\TenantPlanChangeAction;
use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsurePlanCanBeChanged;
use App\Http\Requests\CheckoutRequest;
use App\Models\CreditCard;
use App\Models\Receipt;
use App\Models\Tenant;
use App\Notifications\Tenant\User\SubscriptionSuccessfulNotification;
use App\Repositories\PlanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
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
        /** @var Tenant */
        $tenant = \tenant();

        $plans = $this->planRepository()->getActive();

        $selectedPlan = $tenant?->subscription?->plan ?? $this->planRepository()->getActiveBySlug($request->get('plan', ''));

        $billingInfo = $tenant->billingInfo;

        $creditCards = $tenant->chargeableCards();

        return \view(
            'subscriptions.checkout',
            compact('plans', 'selectedPlan', 'tenant', 'billingInfo', 'creditCards')
        );
    }

    public function create(CheckoutRequest $request)
    {
        $validated = $request->validated();

        $request->authenticate(); // authenticate's card if present

        $plan = $this->planRepository()->getActiveBySlug($validated['plan']);

        $credit_card = $validated['credit_card'];

        if (blank($plan)) {
            throw ValidationException::withMessages([
                'plan' => __('Invalid Plan'),
            ]);
        }

        if ($plan->isFree()) {
            // perform plan downgrade and redirect to dashboard
            return $this->handleSubscriptionPlanChange($plan);
        }

        if ($request->hasChargeableCard($credit_card)) {
            // perform tokenized charge
            return $this->handleCardPaymentRequest($plan, $validated);
        }

        return $this->handlePlanCheckoutRequest($plan, $validated);
    }

    public function update(Request $request)
    {
        $status = $request->status;

        //if payment is successful
        if ($status != 'successful') {
            return \back()->with('checkout_error', $request['message']);
        }

        $transactionID = Flutterwave::getTransactionIDFromCallback();

        $response = Flutterwave::verifyTransaction($transactionID);

        return $this->processVerificationResponse($response['data']);
    }

    protected function handlePlanCheckoutRequest(Plan $plan, array $data)
    {
        try {
            $tenant = tenant();

            //This generates a payment reference
            $reference = Flutterwave::generateReference();

            //calculate prorated Billing
            $amount = \calculateProratedAmount($plan, $tenant->subscription);

            // Enter the details of the payment
            $data = [
                'tx_ref' => $reference,
                'payment_options' => 'card',
                'amount' => $amount,
                'currency' => $plan->currency,
                'redirect_url' => route('filament.plans.checkout.update'),
                'customer' => [
                    'name' => auth()->user()->name,
                    'email' => $tenant->email,
                    'phone_number' => auth()->user()->phone_number,
                ],
                'meta' => [
                    'name' => $data['name'],
                    'tenant' => $tenant->id,
                    'plan' => $data['plan'],
                    'organization' => $data['organization'],
                    'tax_number' => $data['tax_number'],
                    'address' => $data['address'],
                    'zip_code' => $data['zip_code'],
                ],
                'customizations' => [
                    'title' => 'Plan Checkout',
                    'description' => "Payment for {$plan->name} plan",
                ],
            ];

            $payment = Flutterwave::initializePayment($data);

            if ($payment['status'] != 'success') {
                // notify something went wrong
                return \back()->with('checkout_error', $payment['message']);
            }

            return redirect()->to($payment['data']['link']);
        } catch (\Exception $exp) {
            return back(400)->with('checkout_error', $exp->getMessage());
        }
    }

    /**
     * @todo can be refactored to a queuable action or job
     */
    protected function handleCardPaymentRequest(Plan $plan, array $data)
    {
        /** @var Tenant */
        $tenant = tenant();

        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        //calculate prorated Billing
        $amount = \calculateProratedAmount($plan, $tenant->subscription);

        /** @var \App\Models\CreditCard */
        $card = $tenant->chargeableCards()->firstWhere('uuid', $data['credit_card']);

        // Enter the details of the payment
        $data = [
            'tx_ref' => $reference,
            'token' => $card->token,
            'email' => $tenant->email,
            'currency' => $plan->currency,
            'amount' => $amount,
            'narration' => "Payment for Smart-tapp subscription",
            'full_name' => auth()->user()->name,
            'meta' => [
                'tenant' => $tenant->id,
                'name' => $data['name'],
                'plan' => $data['plan'],
                'organization' => $data['organization'],
                'tax_number' => $data['tax_number'],
                'address' => $data['address'],
                'zip_code' => $data['zip_code'],
            ],
        ];

        try {
            //---------------------InitializeTokenizedChargeJob----------------------------//
            /** @todo refactor to queued job */
            $response = Flutterwave::initializeTokenizedCharge($data);
            
            if ($response['status'] != 'success') {
                // notify something went wrong
                return \back()->with('checkout_error', $response['message']);
            }
            
            $transactionID = Arr::get($response, 'data.id');
            //-------------------------------------------------------//
            
            //---------------------ProcessTokenizedChargeJob----------------------------//
            /** @todo refactor to queued job */
            $verif_response = Flutterwave::verifyTransaction($transactionID);
            
            // validate transaction and update subscription
            return $this->processVerificationResponse($verif_response['data']);
            //-------------------------------------------------------//
        } catch (\Exception $exp) {
            return back(400)->with('checkout_error', $exp->getMessage());
        }
    }

    /**
     * processes the verification response from flw:
     * - create receipt
     * - update card
     * - handle subscription
     */
    protected function processVerificationResponse(array $data)
    {
        try {
            $plan = $this->planRepository()->getActiveBySlug($data['meta']['plan']);

            // can throw exception
            if (!$this->validatePayment($plan, $data)) {
                return \back()->with('checkout_error', 'Unable to complete checkout');
            }

            // Success! Confirm the customer's payment
            $tenant = getTenant($data['meta']['tenant']);

            if ($tenant->isNot(tenant())) {
                return \back()->with('checkout_error', 'Unable to complete checkout and subscription to plan');
            }

            DB::beginTransaction(); // Tell Laravel all the code beneath this is a transaction

            $this->createReceipt($tenant, $data);
            $this->updatePaymentMethod($tenant, $data['card']);
            $result = $this->handleSubscriptionPlanChange($plan);

            DB::commit(); // Tell Laravel this transacion's all good and it can persist to DB

            return $result;
        } catch (\Exception $exp) {
            DB::rollBack(); // Tell Laravel, "It's not you, it's me. Please don't persist to DB"

            return back(400)->with('checkout_error', $exp->getMessage());
        }
    }

    /**
     * validates the payment by the customer at flw
     *
     * @throws InvalidArgumentException
     */
    protected function validatePayment(?Plan $plan, array $data)
    {
        if (isset($plan)) {
            $tenant = tenant();
            $amount = \calculateProratedAmount($plan, $tenant->subscription);

            return $data['status'] == 'successful' && $data['amount'] >= $amount && $data['currency'] == $plan->currency;
        }

        return false;
    }

    /**
     * handles the processes involved in changing the plan 
     */
    protected function handleSubscriptionPlanChange(Plan $plan)
    {
        $tenant = tenant();

        // perform plan downgrade and redirect to dashboard
        $subscription = $this->tenantPlanChangeAction()->handle($tenant, $plan);

        if (blank($subscription)) {
            return back()->with('checkout_error', 'Unable to complete plan checkout');
        }

        /** send notification to admin */
        FacadesNotification::sendNow(\auth()->user(), new SubscriptionSuccessfulNotification($subscription));

        return \redirect()->route('filament.pages.settings');
    }

    /**
     * handles updating or creating a new payment method for the customer
     */
    protected function updatePaymentMethod(Tenant $tenant, array $data): CreditCard
    {
        return \tenancy()->central(function () use ($tenant, $data) {
            return $tenant->createCreditCard(
                [
                    'token' => $data['token'],
                    'first_6' => $data['first_6digits'],
                    'last_4' => $data['last_4digits'],
                    'issuer' => $data['issuer'],
                    'country' => $data['country'],
                    'type' => $data['type'],
                    'expiry' => $data['expiry'],
                ]
            );
        });
    }

    /**
     * handles creating a new payment receipt for the customer
     */
    protected function createReceipt(Tenant $tenant, array $data): Receipt
    {
        return \tenancy()->central(function () use ($tenant, $data) {
            return $tenant->createReceipt([
                'currency' => $data['currency'],
                'amount' => $data['amount'],
                'organization' => $data['meta']['organization'],
                'name' => $data['meta']['name'] ?? $data['customer']['name'],
                'email' => $data['meta']['email'] ?? $data['customer']['email'],
                'tax_number' => $data['meta']['tax_number'],
                'address' => $data['meta']['address'],
                'zip_code' => $data['meta']['zip_code'],
            ]);
        });
    }
}
