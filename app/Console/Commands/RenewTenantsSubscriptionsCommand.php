<?php

namespace App\Console\Commands;

use App\Jobs\RenewTenantsSubscriptionJob;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Console\Isolatable;

class RenewTenantsSubscriptionsCommand extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renews overdue and non-cancelled tenants subscriptions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        app(Dispatcher::class)->dispatch(new RenewTenantsSubscriptionJob());

        return Command::SUCCESS;
    }
}
