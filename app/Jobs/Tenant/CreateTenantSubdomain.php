<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateTenantSubdomain implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $tenant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $tenant_domain = $this->tenant->domain;

        if (blank($tenant_domain)) {
            return false;
        }

        if ($this->tenant->domains()->first()) {
            return true;
        }

        $createdDomain = $this->tenant->createDomain($tenant_domain);

        if (blank($createdDomain)) {
            return false;
        }

        return true;
    }
}
