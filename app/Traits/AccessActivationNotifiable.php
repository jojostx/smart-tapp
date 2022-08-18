<?php

namespace App\Traits;

use App\Models\Tenant\User;
use App\Notifications\Tenant\Driver\AccessActivationNotification;
use Illuminate\Database\Eloquent\Model;

trait AccessActivationNotifiable
{
  /**
   * send the access activation notification (SMS) to the access' driver phone number.
   * @param \App\Models\Tenant\User $user
   * 
   * @return void
   */
  public function sendAccessActivationNotification(?Model $admin = null): void
  {
    $admin = $admin ?? auth()->user();

    $this->driver->notify(new AccessActivationNotification($this, $admin));
  }
}
