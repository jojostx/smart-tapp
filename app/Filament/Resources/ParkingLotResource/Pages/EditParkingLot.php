<?php

namespace App\Filament\Resources\ParkingLotResource\Pages;

use App\Filament\Resources\ParkingLotResource;
use App\Models\Tenant\ParkingLot;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParkingLot extends EditRecord
{
    protected static string $resource = ParkingLotResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading(fn (): string => 'Delete Parking Lot')
                ->modalWidth('md')
                ->modalSubheading(fn (ParkingLot $record): string => "Are you sure you want to delete the Parking Lot [{$record->name}]? Doing so will delete all Accesses assigned to it.")
                ->form([
                    TextInput::make("current_password")
                        ->required()
                        ->password()
                        ->rule("current_password"),
                ]),
        ];
    }
}
