<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class DeleteOldUnverifiedTenantsCommand extends Command
{
    protected $signature = 'tenant:delete-old-unverified';

    public function handle()
    {
        $this->info('Deleting old unverified tenants...');

        $count = Tenant::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays(10))
            ->delete();

        $this->comment("Deleted {$count} unverified tenants.");

        $this->info('All done!');
    }
}