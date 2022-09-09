<?php

namespace App\Filament\Resources\ReparkRequestResource\Pages;

use App\Filament\Resources\ReparkRequestResource;
use App\Models\Tenant\Access;
use App\Models\Tenant\ReparkRequest;
use App\Notifications\Tenant\User\ReparkRequestCreatedNotification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class CreateReparkRequest extends CreateRecord
{
    protected static string $resource = ReparkRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $blocker_access = Access::find($data['blocker_access_id']);
        $blockee_access = Access::find($data['blockee_access_id']);

        $shouldNotify = (bool) $data['shouldNotify'];

        $reparkRequest = ReparkRequest::createFromAccess($blocker_access, $blockee_access);

        if (blank($reparkRequest)) {
            FilamentNotification::make($reparkRequest->uuid)
                ->title('Unable to create Repark Request!')
                ->danger();

            return $reparkRequest;
        }

        $notifiables = $blocker_access->issuer->isNot($blockee_access->issuer) ? [
            $blocker_access->issuer,
            $blockee_access->issuer,
        ] : [
            $blocker_access->issuer,
        ];

        /** send notifications to admin and blockers */
        Notification::sendNow($notifiables, new ReparkRequestCreatedNotification($reparkRequest));

        FilamentNotification::make($reparkRequest->uuid)
            ->title('Repark Request created!')
            ->body(fn () => $shouldNotify ? 'The Repark Request notification (text message) has been sent to the Driver of the vehicle that is blocking another!' : 'The Repark Request has been created successfully')
            ->success()
            ->send();

        return $reparkRequest;
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        return null;
    }
}
