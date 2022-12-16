<?php

namespace App\Jobs\Tenant;

use App\Enums\Models\UserAccountStatus;
use App\Models\Tenant;
use App\Models\Tenant\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateTenantAdminUser implements ShouldQueue
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
     * @return
     */
    public function handle()
    {
        /** @param  \App\Models\Tenant  $tenant */
        $this->tenant->run(function ($tenant) {
            // create the user model in the tenant's db and set the tenant_id
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name,
                'email' => $tenant->email,
                'password' => $tenant->password,
            ]);

            $user->forceFill([
                'email_verified_at' => $tenant->email_verified_at,
                'status' => UserAccountStatus::ACTIVE,
            ])->save();
        });
    }
}
