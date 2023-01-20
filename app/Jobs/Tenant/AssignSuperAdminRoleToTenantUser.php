<?php

namespace App\Jobs\Tenant;

use App\Enums\Roles\UserRole;
use App\Models\Tenant;
use App\Models\Tenant\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AssignSuperAdminRoleToTenantUser implements ShouldQueue
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

    public function handle()
    {
        $assigned = $this->tenant->run(function ($tenant) {
            $role = Role::query()
                ->firstOrCreate([
                    'name' => UserRole::SUPER_ADMIN->value,
                    'guard_name' => 'web'
                ]);

            // retrieve the user model in the tenant's db
            $user = User::query()->where('email', $tenant->email)->first();

            if (blank($user)) {
                return false;
            }

            if ($user->hasRole($role) === false) {
                $user->assignRole($role);
            }

            return $user->refresh()->hasRole($role);
        });

        return $assigned;
    }
}
