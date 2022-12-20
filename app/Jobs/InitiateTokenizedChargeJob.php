<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Jojostx\Larasubs\Models\Subscription;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class RenewTenantsSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 0;
    public $maxExceptions = 3;

    public function retryUntil()
    {
        return now()->addHours(12);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // scan subscriptions table for started and inactive subs,
        /** @var \Illuminate\Database\Eloquent\Collection<mixed, Subscription> */
        $subs = Subscription::query()
            ->whereStarted()
            ->whereOverdue()
            ->whereNotCancelled()
            ->get();

        // perform tokenization charge
        $responses = Http::pool(function (Pool $pool) use ($subs) {
            $url = \config('flutterwave.baseUrl');

            return $subs
                ->map(function (Subscription $sub) use ($pool, $url) {
                    // get the default card for the tenants to through their sub
                    $plan = $sub->plan;
                    /** @var Tenant */
                    $tenant = $sub->subscriber;
                    $creditCard = $tenant->defaultCard();
                    $billingInfo = $tenant->billingInfo;

                    $reference = Flutterwave::generateReference();

                    //calculate prorated Billing
                    $amount = \calculateProratedAmount($plan, $sub);

                    // construct the data for performing a tokenized charge for all subs
                    $data = [
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

                    return $pool->as($creditCard->uuid)
                        ->withToken(config('flutterwave.secretKey'))
                        ->post($url . "/tokenized-charges", $data);
                })
                ->toArray();
        });

        // reject failed responses
        $successful_responses = collect($responses)
            ->reject(fn ($response) => $response instanceof \GuzzleHttp\Exception\ConnectException)
            ->reject(function (\Illuminate\Http\Client\Response $response) {
                return $response->failed() || $response->collect()->get('status') != 'success';
            });

        // verify and process
        $successful_responses->each(function ($response) {
            $transactionID = Arr::get($response, 'data.id');

            $verif_response = Flutterwave::verifyTransaction($transactionID);

            return dispatch(new ProcessVerifiedTokenChargeJob($verif_response));
        });
    }
}
