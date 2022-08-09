<?php

namespace App\Traits;

trait AccessActivationNotifiable
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
   * Get the phone number for the notification.
   *
   * @return string
   */
  public function getPhoneNumberForNotification()
  {
    return $this->phone_number_e164;
  }
  
  /**
   * send the access activation notification (SMS) to the access' driver phone number.
   *
   * @return void
   */
  public function sendAccessActivationNotification(): void
  {
    dd('sent');
  }
}
