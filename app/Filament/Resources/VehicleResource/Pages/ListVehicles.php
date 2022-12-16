<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Position;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getActions(): array
    {
        return [];
    }

    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
    }
}
