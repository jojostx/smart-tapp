<?php

namespace App\Listeners\Tenant;


use Illuminate\Notifications\Events\NotificationSent;
use ManeOlawale\Laravel\Termii\Channels\TermiiSmsChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;

class LogSmsNotification
{
  protected $handlers = [
    TermiiSmsChannel::class => LogTermiiNotification::class,
    AfricasTalkingChannel::class => LogAfricasTalkingNotification::class,
  ];

  /**
   * Handle the event.
   *
   * @param  \Illuminate\Notifications\Events\NotificationSent  $event
   * @return void
   */
  public function handle(NotificationSent $event)
  {
    if (!array_key_exists($event->channel, $this->handlers)) {
      return;
    }

    $handler = app()->make($this->handlers[$event->channel]);

    $handler->handle($event);
  }
}
