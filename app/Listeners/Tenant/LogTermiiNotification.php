<?php

namespace App\Listeners\Tenant;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Redis;
use ManeOlawale\Laravel\Termii\Channels\TermiiSmsChannel;

class LogTermiiNotification
{
  /**
   * Handle the event.
   *
   * @param  \Illuminate\Notifications\Events\NotificationSent  $event
   * @return void
   */
  public function handle(NotificationSent $event)
  {
    if ($this->notificationIsLoggable($event->notification, $event->channel)) {
      $notification = DatabaseNotification::query()->find($event->notification->id);
      if (blank($notification)) return;

      $response = $this->extractResponse($event->response);
      $this->updateDBNotificationStatus($notification, $response);

      if (is_array($response)) {
        $this->logNotificationToRedis($notification, $response);
      }
    }
  }

  /**
   * verifies that a notification is a loggable notification.
   * that means the notification is of the TermiiSms class or the Termii class
   */
  public function notificationIsLoggable(\Illuminate\Notifications\Notification $notification, string $channel): bool
  {
    return !($notification instanceof \Illuminate\Notifications\DatabaseNotification) &&
      $channel == TermiiSmsChannel::class;
  }

  /**
   * checks if the data passed is an array and
   * if a key exists in the data
   */
  public function hasKey($data, string $key): bool
  {
    return is_array($data) && array_key_exists($key, $data);
  }

  /**
   * update the Database notification that corresponds to the
   * loggable notification
   */
  public function updateDBNotificationStatus(DatabaseNotification $notification, array|string $data)
  {
    return $notification
      ->forceFill([
        'data->status' => $this->hasKey($data, 'status') ? $data['status'] : 'unknown',
        'data->response' => $this->hasKey($data, 'data') ? $data['data'] : $data,
      ])->save();
  }

  /**
   * logs the message id and notification id to redis for future retrieval
   */
  public function logNotificationToRedis(DatabaseNotification $notification, array $data)
  {
    $messageId = collect($data)
      ->flattenWithKeys()
      ->first(fn ($_v, $key) => str($key)->contains('message_id_str') ||
        str($key)->contains('message_id') ||
        str($key)->contains('origid') ||
        str($key)->contains('notify_id')
      );

    if (filled($messageId) && ($tenant_id = tenant('id'))) {
      Redis::sadd($messageId,  [$tenant_id, $notification->id]);
      Redis::expire($messageId, 3600);
    }
  }
  
  /**
   * extract the response a string or array
   */
  public function extractResponse($response): string|array
  {
    // note in this case, response is tested to make sure it is an array or object
    if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
      $response = $response->collect()->toArray();
    } elseif (is_object($response) && method_exists($response, 'toArray')) {
      $response = $response->toArray();
    } elseif (is_array($response)) {
      $response = $response;
    } else {
      $response = serialize($response);
    }

    return $response;
  }
}
