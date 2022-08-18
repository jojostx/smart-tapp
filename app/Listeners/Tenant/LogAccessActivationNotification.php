<?php

namespace App\Listeners\Tenant;

use App\Notifications\Tenant\Driver\AccessActivationNotification;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;

class LogAccessActivationNotification
{
  /**
   * Handle the event.
   *
   * @param  \Illuminate\Notifications\Events\NotificationSent  $event
   * @return void
   */
  public function handle(NotificationSent $event)
  {
    $response = $event->response;

    if ($event->channel == AfricasTalkingChannel::class) {
      if (
        $event->notification instanceof AccessActivationNotification &&
        !($response instanceof \Illuminate\Notifications\DatabaseNotification)
      ) {
        // note in this case, response is tested to make sure it is an array or object
        if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
          $response = $response->collect()->toArray();
        } else if (is_object($response) && method_exists($response, 'toArray')) {
          $response = $response->toArray();
        } else if (is_array($response)) {
          $response = $response;
        } else {
          $response = serialize($response);
        }

        $notification = DatabaseNotification::find($event->notification->id);

        $hasStatusKey = is_array($response) && array_key_exists('status', $response);
        $hasDataKey = is_array($response) && array_key_exists('data', $response);

        if ($notification) {
          $notification->forceFill(
            $hasStatusKey ?
              [
                'data->status' => $response['status'],
                'data->response' => $hasDataKey ? $response['data'] : $response
              ] :
              [
                'data->status' => 'unknown',
                'data->response' => $response
              ]
          )->save();

          if (is_array($response)) {
            $messageId = collect($response)->flattenWithKeys()->first(function ($value, $key) {
              return str($key)->contains('messageId');
            });
  
            if (filled($messageId) && tenant('id')) {
              Redis::command('SADD', [$messageId, tenant('id'), $notification->id]);
            }
          }
        }
      }
    }
  }
}
