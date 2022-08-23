<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Filament\Components\RefreshListPageTableComponent;
use App\Filament\Resources\AccessResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Notifications\DatabaseNotification;

class ListAccesses extends ListRecords
{
    protected static string $resource = AccessResource::class;

    protected function getListeners()
    {
        return ['checkNotificationStatus'];
    }

    public function checkNotificationStatus(?string $notification_id = '')
    {
        /** @var \Illuminate\Support\Collection $notificationData */
        $notificationData = collect(DatabaseNotification::whereNull('read_at')
            ->where('id', $notification_id)
            ->first()?->data ?? [])->flattenWithKeys();

        if (blank($notificationData)) {
            return;
        }

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
                ->title('Activation Notification sent successfully')
                ->success()
                ->send();
        } else {
            Notification::make('delivery-failed')
                ->title('Unable to Send Activation Notification.')
                ->danger()
                ->send();
        }
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTablePollingInterval(): ?string
    {
        return '30s';
    }
}
