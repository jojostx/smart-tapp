<?php

namespace App\Filament\Traits;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification as FacadesNotification;

trait CanQueryNotificationSentStatus
{
    /**
     * checks if the "status" property of the `data` json column of the database notification with id == notification_id is set to "success"
     *
     * @param  string  $notification_id
     * @return void
     */
    public function checkNotificationStatus(?string $notification_id = '')
    {
        $notification = DatabaseNotification::query()
          ->where('id', $notification_id)
          ->first();

        if (blank($notification)) {
            return;
        }

        /** @var \Illuminate\Support\Collection $notificationData */
        $notificationData = collect($notification->data ?? [])->flattenWithKeys();

        // if the status is 'success' send a filament notification to alert the admin that the notification was successfully sent
        $wasDelivered = $notificationData
          ->filter(function ($value, $key) {
              return str($key)->endsWith('status');
          })
          ->every(function ($value) {
              return in_array(strtolower($value), ['success', 'sent']);
          });

        if ($wasDelivered) {
            Notification::make('delivered')
              ->title('Notification sent successfully')
              ->success()
              ->send();
        } else {
            Notification::make('delivery-failed')
              ->title('Unable to Send Notification.')
              ->danger()
              ->actions([
                  $this->canResendNotification() &&
                  Action::make('resend')
                    ->button()
                    ->color('secondary')
                    ->emit('resendFailedSMSNotification', ['notification' => $notification, 'channel' => 'termii']),
              ])
              ->send();
        }
    }

    /**
     * Restores the laravel notification instance from the database Notification's Data column
     * and resends it via the notification's fallback channels
     *
     * @param  DatabaseNotification  $db_notification
     * @param  string  $channel
     *
     * return void
     */
    public function resendFailedSMSNotification(DatabaseNotification $db_notification, ?string $channel = 'termii')
    {
        $notifiable_type = $db_notification->notifiable_type;

        $notifiable = $notifiable_type::find($db_notification->notifiable_id);

        $sendable_notification = filled($db_notification->data['notification']) ? unserialize($db_notification->data['notification']) : null;

        if (blank($sendable_notification) || blank($notifiable)) {
            return;
        }

        FacadesNotification::sendNow($notifiable, $sendable_notification, $sendable_notification->fallbackChannels() ?? ['termii']);
    }

    /**
     * verifies that 'resendFailedSMSNotification' key exists in the livewire component's listeners array
     *
     * @return bool
     */
    public function canResendNotification(): bool
    {
        return is_array($this->getListeners()) && in_array('resendFailedSMSNotification', $this->getListeners());
    }
}
