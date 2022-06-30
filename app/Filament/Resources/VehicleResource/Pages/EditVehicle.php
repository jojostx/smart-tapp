<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->form([
                    TextInput::make("current_password")
                        ->required()
                        ->password()
                        ->rule("current_password"),
                ]),
        ];
    }
}
