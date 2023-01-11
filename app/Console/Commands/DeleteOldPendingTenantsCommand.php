<?php

namespace App\Console\Commands;

use App\Models\PendingTenant;
use Illuminate\Console\Command;

class DeleteOldPendingTenantsCommand extends Command
{
    protected $signature = 'tenant:delete-old-pending';

    /**
     * @todo send reminder mail to user for them to
     * complete their account creation
     */
    public function handle()
    {
        $this->info('Deleting pending tenants...');

        $count = PendingTenant::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays(10))
            ->delete();

        $this->comment("Deleted {$count} pending tenants.");

        $this->info('All done!');
    }
}
