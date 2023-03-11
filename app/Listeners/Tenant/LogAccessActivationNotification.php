<?php

namespace App\Listeners\Tenant;

use App\Notifications\Tenant\Driver\AccessActivationNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Events\NotificationSent;
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

        if (
            $this->notificationIsLoggable($event->notification) &&
            $event->channel == AfricasTalkingChannel::class
        ) {
            $notification = DatabaseNotification::query()->find($event->notification->id);
            $response = $this->extractResponse($response);

            if (blank($notification)) return;

            $this->updateDBNotificationStatus($notification, $response);

            if (is_array($response)) {
                $this->logNotificationToRedis($notification, $response);
            }
        }
    }

    public function notificationIsLoggable(\Illuminate\Notifications\Notification $notification): bool
    {
        return $notification instanceof AccessActivationNotification &&
            !($notification instanceof \Illuminate\Notifications\DatabaseNotification);
    }

    public function extractResponse($response): mixed
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

    public function hasKey($data, string $key): bool
    {
        return is_array($data) && array_key_exists($key, $data);
    }

    public function updateDBNotificationStatus(DatabaseNotification $notification, array $data)
    {
        return $notification->forceFill(
            $this->hasKey($data, 'status') ?
                [
                    'data->status' => $data['status'],
                    'data->response' => $this->hasKey($data, 'data') ? $data['data'] : $data,
                ] :
                [
                    'data->status' => 'unknown',
                    'data->response' => $data,
                ]
        )->save();
    }

    public function logNotificationToRedis(DatabaseNotification $notification, array $data)
    {
        $messageId = collect($data)
            ->flattenWithKeys()
            ->first(fn ($_v, $key) => str($key)->contains('messageId') || str($key)->contains('id'));

        if (filled($messageId) && ($tenant_id = tenant('id'))) {
            Redis::sadd($messageId,  [$tenant_id, $notification->id]);
            Redis::expire($messageId, 3600);
        }
    }
}
