<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Resources\DriverResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Position;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected function getActions(): array
    {
        return [];
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
