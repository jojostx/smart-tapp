<?php

namespace App\Filament\Resources\ReparkRequestResource\Pages;

use App\Filament\Resources\ReparkRequestResource;
use App\Models\Tenant\ReparkRequest;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReparkRequests extends ListRecords
{
    protected static string $resource = ReparkRequestResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
