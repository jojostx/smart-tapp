<?php

namespace App\Filament\Resources\ReparkRequestResource\Pages;

use App\Filament\Resources\ReparkRequestResource;
use App\Filament\Traits\CanQueryNotificationSentStatus;
use App\Notifications\Tenant\Driver\AccessActivationNotification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Notifications\DatabaseNotification;

class ListReparkRequests extends ListRecords
{
    use CanQueryNotificationSentStatus;

    protected static string $resource = ReparkRequestResource::class;

    protected function getListeners()
    {
        return ['checkNotificationStatus'];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
