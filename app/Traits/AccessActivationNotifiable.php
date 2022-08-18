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
   * 
   * @return string
   */
  public function sendAccessActivationNotification(?Model $admin = null): string
  {
    $admin = $admin ?? auth()->user();
    $id = Str::uuid()->toString();

    $this->driver->notify(new AccessActivationNotification($this, $admin, $id));

    return $id;
  }
}
