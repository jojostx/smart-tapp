<?php

namespace App\Console\Commands;

use App\Jobs\Tenant\DeleteTenantDatabase;
use App\Jobs\Tenant\DeleteTenantSubdomain;
use App\Models\Tenant;
use Illuminate\Console\Command;

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
            // delete tenant db
            app(Dispatcher::class)->dispatch(new DeleteTenantDatabase($tenant));

            // delete tenant domains
            app(Dispatcher::class)->dispatch(new DeleteTenantSubdomain($tenant));
        });

        $count = Tenant::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays(10))
            ->delete();

        $this->comment("Deleted {$count} unverified tenants.");

        $this->info('All done!');
    }
}
