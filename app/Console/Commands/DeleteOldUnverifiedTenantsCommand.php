<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOldUnverifiedTenantsCommand extends Command
{
    protected $signature = 'tenant:delete-old-unverified';

    public function handle()
    {
        $this->info('Deleting old unverified tenants...');

        $oldUnverifiedTenants = Tenant::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays(10))
            ->select('id')
            ->get();

        $oldUnverifiedTenants->each(function (Tenant $tenant) {
            $db = \config('tenancy.database.prefix') . $tenant->getTenantKey() . config('tenancy.database.suffix');
            DB::select("DROP DATABASE IF EXISTS `$db`");
        });

        $count = Tenant::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays(10))
            ->delete();

        $this->comment("Deleted {$count} unverified tenants.");

        $this->info('All done!');
    }
}
