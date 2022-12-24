<?php

namespace App\Actions;

use App\Enums\Models\UserAccountStatus;
use App\Enums\Roles\UserRole;
use App\Events\Tenant\TenantAdminUserCreated;
use App\Models\Tenant;
use App\Models\Tenant\User;

class CreateTenantAdminUserAction
{
    // if plan is free and the tenant has no subscription
    // or the tenant has a higher subscription plan, perform the plan downgrade action.
    // change plan for current subscription and allow sync
    public function __invoke(Tenant $tenant): ?User
    {
        try {
            $user = $tenant->run(function ($tenant) {
                // create the user model in the tenant's db and set the tenant_id
                $user = User::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'email' => $tenant->email,
                ],
                [
                    'name' => $tenant->name,
                    'password' => $tenant->password,
                ]);

                $saved = $user->forceFill([
                    'email_verified_at' => $tenant->email_verified_at,
                    'status' => UserAccountStatus::ACTIVE,
                ])->save();

                if (\filled($user) && $saved && !$user->hasRole(UserRole::SUPER_ADMIN->value)) {
                    $user->assignRole(UserRole::SUPER_ADMIN->value);
                }

                return $user;
            });

            \event(new TenantAdminUserCreated($tenant, $user));
        } catch (\Throwable $exp) {
            return null;
        }

        return $user;
    }
}
