<?php

namespace App\Traits;

use App\Models\Tenant\User;
use App\Notifications\Tenant\Driver\ConfirmReparkNotification;
use App\Notifications\Tenant\Driver\ReparkRequestResolvedNotification;
use App\Notifications\Tenant\Driver\ShouldReparkVehicleNotification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as NotificationsNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait ReparkRequestResolutionNotifiable
{
    /**
     * send the ReparkRequest Resolution notification (SMS) to the blocking driver's phone number.
     *
     * @param  \App\Models\Tenant\User|null  $admin
     * @param  int  $checkStatusCountdown
     * @return string
     */
    public function sendReparkRequestResolutionNotification(?Model $admin = null, int $checkStatusCountdown = 0): string
    {
        $id = Str::uuid()->toString();

        // ShouldReparkVehicleNotification -> blocking Driver
        // ResolvingReparkRequestNotification -> blocked driver
        $this->blockerDriver->notify(new ShouldReparkVehicleNotification($this, $admin, $id, $checkStatusCountdown));

        return $id;
    }

    /**
     * send the Repark confirmation notification (SMS) to the blocked driver's phone number.
     *
     * @param  \App\Models\Tenant\User|null  $admin
     * @param  int  $checkStatusCountdown
     * @return string
     */
    public function sendConfirmReparkNotification(?Model $admin = null, int $checkStatusCountdown = 0): string
    {
        $id = Str::uuid()->toString();

        $this->blockeeDriver->notify(new ConfirmReparkNotification($this, $admin, $id, $checkStatusCountdown));

        return $id;
    }

    /**
     * send the ReparkRequest Resolved notification (SMS) to the blocked driver's phone number.
     *
     * @param  \App\Models\Tenant\User|null  $admin
     * @param  int  $checkStatusCountdown
     * @return string
     */
    public function sendReparkRequestResolvedNotification(?Model $admin = null, int $checkStatusCountdown = 0): string
    {
        $id = Str::uuid()->toString();

        $this->blockeeDriver->notify(new ReparkRequestResolvedNotification($this, $admin, $id, $checkStatusCountdown));

        return $id;
    }
}
