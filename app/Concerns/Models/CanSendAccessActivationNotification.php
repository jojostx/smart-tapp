<?php

namespace App\Concerns\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface CanSendAccessActivationNotification
{
  /**
   * Get the driver to be notified.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function driver(): BelongsTo;

  /**
   * send the access activation notification (SMS) to the access' driver phone number.
   *
   * @return void
   */
  public function sendAccessActivationNotification();
}
