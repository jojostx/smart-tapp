<?php

namespace App\Jobs;

use App\Actions\TenantPlanChangeAction;
use App\Models\CreditCard;
use App\Models\Receipt;
use App\Models\Tenant;
use App\Repositories\PlanRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptionsWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Jojostx\Larasubs\Models\Plan;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class ProcessVerifiedTokenChargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $maxExceptions = 3;

    public function retryUntil()
    {
        return now()->addHours(12);
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $data)
    {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [(new ThrottlesExceptionsWithRedis(10, 5))->by('flw_api_verify_charge')];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // verify and process
        $transactionID = Arr::get($this->data, 'data.id');

        if (blank($transactionID)) {
            return false;
        }

        $verif_response = Flutterwave::verifyTransaction($transactionID);

        return $this->processVerificationResponse($verif_response['data']);
    }

    /**
     * processes the verification response from flw:
     * - create receipt
     * - update card
     * - handle subscription
     */
    protected function processVerificationResponse(array $data)
    {
        $planRepository = new PlanRepository;
        $tenantPlanChangeAction = app(TenantPlanChangeAction::class);

        try {
            $tenant = getTenant($data['meta']['tenant']);
            $plan = $planRepository->getActiveBySlug($data['meta']['plan']);

            if (!$this->validatePayment($tenant, $plan, $data)) {
                return false;
            }

            // Success! Confirm the customer's payment
            DB::beginTransaction();

            $this->createReceipt($tenant, $data);
            $this->updatePaymentMethod($tenant, $data['card']);
            $result = $tenantPlanChangeAction->handle($tenant, $plan);

            DB::commit();

            return $result;
        } catch (\Exception $exp) {
            DB::rollBack();

            return false;
        }
    }

    protected function validatePayment(?Tenant $tenant, ?Plan $plan, array $data)
    {
        if (isset($plan) || filled($tenant)) {
            $amount = \calculateProratedAmount($plan, $tenant->subscription);

            return $data['status'] == 'successful' && $data['amount'] >= $amount && $data['currency'] == $plan->currency;
        }

        return false;
    }

    /**
     * handles updating or creating a new payment method for the customer
     */
    protected function updatePaymentMethod(Tenant $tenant, array $data): CreditCard
    {
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
    }

    /**
     * handles creating a new payment receipt for the customer
     */
    protected function createReceipt(Tenant $tenant, array $data): Receipt
    {
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
    }
}
