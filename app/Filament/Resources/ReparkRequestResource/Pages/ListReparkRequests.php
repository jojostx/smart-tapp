<?php

namespace App\Filament\Resources\ReparkRequestResource\Pages;

use App\Filament\Resources\ReparkRequestResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Position;

class ListReparkRequests extends ListRecords
{
    protected static string $resource = ReparkRequestResource::class;

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

    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
    }
}
