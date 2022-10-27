<?php

namespace App\Filament\Resources\ParkingLotResource\Pages;

use App\Filament\Resources\ParkingLotResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Position;

class ListParkingLots extends ListRecords
{
    protected static string $resource = ParkingLotResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
    }
}
