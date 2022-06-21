<?php

namespace App\Listeners\Tenant;

use App\Models\SessionModel;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TenantUserLoggedOut
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        if ($event->user->tenant_id) {
            $sessionModel = SessionModel::where([
                'tenant_id' => $event->user->tenant_id,
                'user_id' => $event->user->id,
            ]);

            \logger($sessionModel);
        }
    }
}
