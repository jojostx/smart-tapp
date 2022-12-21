<?php

namespace App\Jobs;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Jojostx\Larasubs\Models\Subscription;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class RenewTenantsSubscriptionJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // scan subscriptions table for started and inactive subs,
        /** @var \Illuminate\Database\Eloquent\Collection<mixed, Subscription> */
        $subscriptions = Subscription::query()
            ->whereStarted()
            ->whereOverdue()
            ->whereNotCancelled()
            ->get();

        Bus::batch(
            $subscriptions->map(function (Subscription $subscription) {
                return app(InitializeTokenizedChargeJob::class, compact('subscription'));
            })->toArray()
        )->name('Initialize Tokenized Charges')
        ->allowFailures()
        ->dispatch();
    }
}
