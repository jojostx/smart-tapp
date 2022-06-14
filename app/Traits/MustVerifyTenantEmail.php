<?php

namespace App\Traits;

use Closure;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Tzsk\Otp\Facades\Otp;

trait MustVerifyTenantEmail
{
  /**
   * Determine if the user has verified their email address.
   *
   * @return bool
   */
  public function hasVerifiedEmail()
  {
    return !is_null($this->email_verified_at);
  }

  /**
   * Mark the given user's email as verified.
   *
   * @return bool
   */
  public function markEmailAsVerified()
  {
    return $this->forceFill([
      'email_verified_at' => $this->freshTimestamp(),
    ])->save();
  }

  /**
   * Send the email verification OTP notification.
   *
   * @return void
   */
  public function sendEmailVerificationNotification()
  {
    $verificationNotification = new VerifyEmail;

    $verificationNotification::toMailUsing(static::toMailUsing());
    $verificationNotification::createUrlUsing(static::createUrlUsing());

    $this->notify($verificationNotification);
  }

  /**
   * Set a callback that should be used when building the notification mail message.
   *
   * @param  \Closure|null  $callback
   * @return Closure
   */
  public static function toMailUsing(?Closure $callback = null)
  {
    if ($callback) {
      return $callback;
    }

    return function (Model $notifiable, $verificationUrl) {
      // create otp with hash of users email and id,
      $uniqueSecret = sha1($notifiable->getKey() . $notifiable->getEmailForVerification());
    
      $verificationOTP = Otp::generate($uniqueSecret);

      return (new MailMessage)
        ->subject(Lang::get('Verify Email Address'))
        ->greeting('Hello!')
        ->line("Thank you for choosing " . config('app.name', 'Smart-tapp') . ", Use the following OTP to complete your Sign Up procedures. OTP is valid for 5 minutes")
        ->line(Lang::get('Your verification OTP is'))
        ->line($verificationOTP)
        ->line(Lang::get('If you did not create an account, no further action is required.'));
    };
  }

  /**
   * Set a callback that should be used when creating the email verification URL.
   *
   * @param  \Closure  $callback
   * @return void
   */
  public static function createUrlUsing(?Closure $callback = null)
  {
    if ($callback) {
      return $callback;
    }

    // build url here
    return function (Model $notifiable) {
      return route('verification.notice', ['id' => $notifiable->getKey()]);
    };
  }

  /**
   * Get the email address that should be used for verification.
   *
   * @return string
   */
  public function getEmailForVerification()
  {
    return $this->email;
  }
}
