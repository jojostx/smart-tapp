<?php

namespace App\Notifications\Tenant\Driver;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class SendAccessUrlSMS extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * The callback that should be used to create the verify email URL.
   *
   * @var \Closure|null
   */
  public static $createUrlCallback;

  /**
   * The callback that should be used to build the SMS message.
   *
   * @var \Closure|null
   */
  public static $toSMSCallback;

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return [AfricasTalkingChannel::class];
  }

  /**
   * Get the SMS representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return AfricasTalkingMessage
   */
  public function toAfricasTalking($notifiable)
  {
    $verificationUrl = $this->verificationUrl($notifiable);

    if (static::$toSMSCallback) {
      return call_user_func(static::$toSMSCallback, $notifiable, $verificationUrl);
    }

    $message = $this->buildSMSMessage($verificationUrl, $notifiable);

    // return (new AfricasTalkingMessage())
    //   ->content($message);
  }

  /**
   * Get the verify SMS notification message for the given URL.
   *
   * @param  string  $url
   * @param  mixed  $notifiable
   * 
   * @return string
   */
  protected function buildSMSMessage($url, $notifiable)
  {
    $name = (string) $notifiable->first_name ?? '';

    return "\n Hello {$name},\n Click the link below to verify your phone number:\n {$url}";
  }

  /**
   * Get the verification URL for the given notifiable.
   *
   * @param  mixed  $notifiable
   * @return string
   */
  protected function verificationUrl($notifiable)
  {
    return URL::temporarySignedRoute(
      'phoneverification.verify',
      Carbon::now()->addMinutes(Config::get('auth.verification.expire', 15)),
      [
        'id' => $notifiable->getKey(),
        'hash' => sha1($notifiable->getPhoneNumberForVerification()),
      ]
    );
  }

  /**
   * Set a callback that should be used when creating the SMS verification URL.
   *
   * @param  \Closure  $callback
   * @return void
   */
  public static function createUrlUsing(callable $callback)
  {
    static::$createUrlCallback = $callback;
  }

  /**
   * Set a callback that should be used when building the notification SMS message.
   *
   * @param  \Closure  $callback
   * @return void
   */
  public static function toSMSUsing(callable $callback)
  {
    static::$toSMSCallback = $callback;
  }
}
