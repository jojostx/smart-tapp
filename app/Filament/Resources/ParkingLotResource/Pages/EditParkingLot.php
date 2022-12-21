<?php

namespace App\Filament\Resources\ParkingLotResource\Pages;

use App\Filament\Resources\ParkingLotResource;
use App\Filament\Traits\WithCurrentPasswordField;
use App\Models\Tenant\ParkingLot;
use App\Filament\Resources\ParkingLotResource\Widgets;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParkingLot extends EditRecord
{
    use WithCurrentPasswordField;

    protected static string $resource = ParkingLotResource::class;

    protected function getFooterWidgets(): array
    {
        return [
            Widgets\QrcodeWidget::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading(fn (): string => 'Delete Parking Lot')
                ->modalSubheading(fn (ParkingLot $record): string => "Are you sure you want to delete the Parking Lot [{$record->name}]? Doing so will delete all Accesses assigned to it.")
                ->form([
                    static::getCurrentPasswordField(),
                ])
                ->modalWidth('md'),
        ];
    }
}
