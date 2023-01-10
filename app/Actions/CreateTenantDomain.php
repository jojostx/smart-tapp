<?php

namespace App\Actions;

use App\Models\Tenant;
use App\Models\Tenant\User;

class CreateTenantDomain
{
    public function __invoke(Tenant $tenant): ?User
    {
        try {
            throw_if(blank($tenant_domain = $tenant->domain));

            return $tenant->domains()->first() ?? $tenant->createDomain($tenant_domain);
        } catch (\Throwable $exp) {
            return null;
        }
    }
}
