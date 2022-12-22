<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptionsWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Jojostx\Larasubs\Models\Subscription;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class InitializeTokenizedChargeJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function __construct(public Subscription $subscription)
    {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [(new ThrottlesExceptionsWithRedis(10, 10))->by('flw_api_token_charge')];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        if ($this->subscription->plan->isFree()) {
            return $this->subscription->renew();
        }

        $data = $this->buildData();

        $response = $this->initializeCharge($data);

        if ($response->collect()->get('status') != 'success') {
            // cancel the sub this prevents the RenewTenantSubscriptionJob 
            // from picking up the sub for renewal
            $this->subscription->cancel(now());

            return;
        };

        dispatch(new ProcessVerifiedTokenChargeJob($response));
    }

    /**
     * construct the data for performing a tokenized charge
     */
    public function buildData(): array
    {
        $plan = $this->subscription->plan;
        /** @var Tenant */
        $tenant = $this->subscription->subscriber;
        $creditCard = $tenant->defaultCard();
        $billingInfo = $tenant->billingInfo;

        $reference = Flutterwave::generateReference();

        //calculate prorated Billing
        $amount = \calculateProratedAmount($plan, $this->subscription);

        // construct the data for performing a tokenized charge for all subs
        return [
            'tx_ref' => $reference,
            'token' => $creditCard->token,
            'email' => $tenant->email,
            'currency' => $plan->currency,
            'amount' => $amount,
            'full_name' => $tenant->name,
            'narration' => "Payment for Smart-tapp subscription",
            'meta' => [
                'tenant' => $tenant->id,
                'plan' => $plan->slug,
                'name' => $billingInfo?->name,
                'organization' => $billingInfo?->organization ?? $tenant->organization,
                'tax_number' => $billingInfo?->tax_number ?? '',
                'address' => $billingInfo?->address ?? '',
                'zip_code' => $billingInfo?->zip_code ?? '',
            ],
        ];
    }

    /**
     * initialize the charge request
     */
    public function initializeCharge($data)
    {
        return Http::timeout(10)
            ->withToken(config('flutterwave.secretKey'))
            ->post(config('flutterwave.baseUrl') . "/tokenized-charges", $data)
            ->json();
    }
}
