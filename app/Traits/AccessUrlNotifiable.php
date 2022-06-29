<?php

namespace App\Traits;

use App\Notifications\Tenant\Driver\SendAccessUrlSMS;
use NotificationChannels\AfricasTalking\Exceptions\CouldNotSendNotification;

trait AccessUrlNotifiable
{
  /**
   * Determine if the driver has verified their phone number.
   *
   * @return bool
   */
  public function hasVerifiedPhoneNumber()
  {
    return !is_null($this->phone_verified_at);
  }

  /**
   * Mark the given driver's phone number as verified.
   *
   * @return bool
   */
  public function markPhoneNumberAsVerified()
  {
    return $this->forceFill([
      'phone_verified_at' => $this->freshTimestamp(),
    ])->save();
  }

  /**
   * Send the phone number verification notification.
   *
   * @return void
   * 
   * @throws CouldNotSendNotification
   */
  public function sendPhoneNumberNotification()
  {
    // $this->notify(new SendAccessUrlSMS::createUrlUsing());
    // $this->notify(new SendAccessUrlSMS::toSMSUsing());
  }

  /**
   * Get the phone number that should be used for verification.
   *
   * @return string
   */
  public function getPhoneNumberForNotification()
  {
    return $this->phone_number_e164;
  }
}
