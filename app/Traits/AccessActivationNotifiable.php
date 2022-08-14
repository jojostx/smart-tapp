<?php

namespace App\Traits;

use App\Notifications\Tenant\Driver\AccessActivationNotification;

trait AccessActivationNotifiable
{
  /**
   * send the access activation notification (SMS) to the access' driver phone number.
   *
   * @return void
   */
  public function sendAccessActivationNotification(): void
  {
    $this->driver->notify(new AccessActivationNotification($this));
  }
}
