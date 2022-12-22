<?php

namespace App\Jobs\Tenant;

use App\Models\CreditCard;
use App\Models\Tenant;
use App\Models\Tenant\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class UpdateCustomerEmailOnFlutterwave implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $user;

    protected $tenant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Tenant $tenant)
    {
        $this->user = $user;
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Http::pool(function (Pool $pool) {
            /** @var \Illuminate\Database\Eloquent\Collection<mixed, CreditCard> $creditCards */
            $creditCards = $this->tenant->creditCards()->get();

            foreach ($creditCards as $creditCard) {
                $pool
                  ->as($creditCard->uuid)
                  ->withToken(\env('FLW_SECRET_KEY'))
                  ->post(
                      'https://api.flutterwave.com/v3/tokens/' . $creditCard->token,
                      [
                          'name' => $this->user->name,
                          'email' => $this->user->email,
                          'phone_number' => $this->user->phone_number,
                      ]
                  );
            }
        });
    }
}
