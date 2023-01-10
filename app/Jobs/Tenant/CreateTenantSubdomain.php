<?php

namespace App\Jobs\Tenant;

use App\Actions\CreateTenantDomain;
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Tenant $tenant,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(CreateTenantDomain $createTenantDomain)
    {
        return filled($createTenantDomain($this->tenant));
    }
}
