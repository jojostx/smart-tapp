<?php

namespace App\Jobs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessTermiiReportWebhook extends ProcessWebhookJob
{
  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    \logger($this->webhookCall);
    \logger($this->webhookCall->payload);
    \logger($this->webhookCall->headers);
    $this->updateNotificationStatus($this->webhookCall->payload);
  }

  public function updateNotificationStatus(?array $payload)
  {
    if (blank($payload)) return;

    $payload = collect($payload)->flattenWithKeys();

    $messageId = $this->getMessageID($payload);
    $messageStatus = $this->getMessageStatus($payload);

    // retrieve notification data from redis. 
    // example: [$tenant->id, $notification->id] ["9b788b57-bc4f-4c61-a6ba-1fa9c0c909cb", "4ed8aae8-8e1d-4c68-9bd4-00c283f03b81"]
    // see: insertion point in LogTermiiNotification::line(50)
    $result = (array) Redis::command('SMEMBERS', [$messageId]);

    if (filled($result) && count($result) > 1) {
      [$tenant_id, $notification_id] = $result;

      if ($tenant = tenancy()->find($tenant_id)) {
        // find the notification by id within the tenant's context
        $tenant->run(function () use ($notification_id, $messageStatus) {
          // update the status of the notification with the status contained in the request
          filled($messageStatus) && DB::table('notifications')
            ->where('id', $notification_id)
            ->update(['data->status' => $messageStatus]);
        });
      }
    }
  }

  protected function getMessageID($payload)
  {
    return collect($payload)
      ->flattenWithKeys()
      ->first(
        fn ($_v, $key) => str($key)->contains('message_id_str') ||
          str($key)->contains('message_id') ||
          str($key)->contains('origid') ||
          str($key)->contains('notify_id')
      );
  }

  protected function getMessageStatus($payload)
  {
    return collect($payload)
      ->flattenWithKeys()
      ->first(
        fn ($_v, $key) => str($key)->contains('messagestate') ||
          str($key)->contains('status')
      );
  }
}
