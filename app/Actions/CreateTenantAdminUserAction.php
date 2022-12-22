<?php

namespace App\Actions;

use App\Enums\Models\UserAccountStatus;
use App\Enums\Roles\UserRole;
use App\Events\Tenant\TenantAdminUserCreated;
use App\Models\Tenant;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\DB;

class CreateTenantAdminUserAction
{
    // if plan is free and the tenant has no subscription
    // or the tenant has a higher subscription plan, perform the plan downgrade action.
    // change plan for current subscription and allow sync
    public function __invoke(Tenant $tenant): ?User
    {
        $user = $tenant->run(function ($tenant) {
            // create the user model in the tenant's db and set the tenant_id
            try {
                DB::beginTransaction();

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

                $user->assignRole(UserRole::SUPER_ADMIN->value);

                DB::commit();

                \event(new TenantAdminUserCreated($tenant, $user));

                return $user;
            } catch (\Exception $exp) {
                DB::rollBack();

                return null;
            }
        });

        return $user;
    }
}
