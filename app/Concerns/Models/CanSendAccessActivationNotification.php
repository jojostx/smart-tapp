<?php

namespace App\Concerns\Models;

interface CanSendAccessActivationNotification
{
  /**
   * Determine if the driver has verified their phone number.
   *
   * @return bool
   */
  public function hasVerifiedPhoneNumber();

  /**
   * Mark the given driver's phone number as verified.
   *
   * @return bool
   */
  public function markPhoneNumberAsVerified();

  /**
   * Get the phone number for the notification.
   *
   * @return string
   */
  public function getPhoneNumberForNotification();
  
  /**
   * send the access activation notification (SMS) to the access' driver phone number.
   *
   * @return bool
   */
  public function sendAccessActivationNotification();
}