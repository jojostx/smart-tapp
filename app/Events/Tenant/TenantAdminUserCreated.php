<?php

namespace App\Events\Tenant;

use App\Models\Tenant;
use App\Models\Tenant\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stancl\Tenancy\Events\Contracts\TenantEvent;

class TenantAdminUserCreated extends TenantEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /** @var Tenant */
    public $tenant;

    /** @var User */
    public $user;

    public function __construct(Tenant $tenant, User $user)
    {
        $this->tenant = $tenant;
        $this->user = $user;
    }
}
