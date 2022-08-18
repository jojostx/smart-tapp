<?php

namespace App\Notifications\Tenant\Driver;

use App\Models\Tenant\Access;
use App\Models\Tenant\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;
use ManeOlawale\Laravel\Termii\Messages\Message as TermiiMessage;
use Illuminate\Notifications\Notification;

class AccessActivationNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(public Access $access, public User $admin, ?string $id = '')
  {
    if (filled($id)) {
      $this->id = $id;
    }

    $this->afterCommit();
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['database', AfricasTalkingChannel::class];
  }

  /**
   * Get the AfricasTalking SMS representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return AfricasTalkingMessage
   */
  public function toAfricasTalking($notifiable)
  {
    $message = $this->buildSMSMessage($notifiable);

    return (new AfricasTalkingMessage())
      ->content($message);
  }

  /**
   * Get the termii SMS representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \ManeOlawale\Laravel\Termii\Messages\Message
   */
  public function toTermii($notifiable)
  {
    $message = $this->buildSMSMessage($notifiable);

    return (new TermiiMessage())
      ->content($message);
  }

  /**
   * Get the verify SMS notification message for the given URL.
   *
   * @param  mixed  $notifiable
   * 
   * @return string
   */
  protected function buildSMSMessage($notifiable): string
  {
    $key = ((string) $this->access->id) . str($this->access->uuid)->before('-')->value();
    $url = tenant_route(tenant()->domain, 'access.redirect', compact('key'));
    $plate_number = $this->access->vehicle->plate_number;

    $message = "Hello, Click the link below to activate your access for vehicle [{$plate_number}]:";
    $message .= "\n{$url}\n";
    $message .= \boolval($this->access->expiry_period) ? "  This Access expires in {$this->access->expiry_period} minutes" : "";

    return $message;
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      'admin_id' => auth()->id() ?? $this->admin->id,
      'access_id' => $this->access->id,
    ];
  }

  public function failed(\Exception $exception)
  {
    if ($exception instanceof GuzzleException) {
      // set the status of the notification to failed in the notifications table:
      /** @var \Illuminate\Database\Eloquent\Model|null $notification */
      $notification = DatabaseNotification::find($this->id);

      if (filled($notification)) {
        $notification->forceFill([
          'data->status' => 'failed'
        ])->save();
      }
    }
  }
}
