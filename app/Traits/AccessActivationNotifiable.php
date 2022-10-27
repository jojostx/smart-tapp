<?php

namespace App\Traits;

use App\Notifications\Tenant\Driver\AccessActivationNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait AccessActivationNotifiable
{
  /**
   * send the access activation notification (SMS) to the access' driver phone number.
   * 
   * @param \App\Models\Tenant\User $user
   * @param int $checkStatusCountdown
   * 
   * @return string
   */
  public function sendAccessActivationNotification(?Model $admin = null, int $checkStatusCountdown = 0): string
  {
    $admin = $admin ?? auth()->user();
    $id = Str::uuid()->toString();

    $this->driver->notify(new AccessActivationNotification($this, $admin, $id, $checkStatusCountdown));

    return $id;
  }
}
