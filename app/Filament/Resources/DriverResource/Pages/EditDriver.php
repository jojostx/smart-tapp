<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Forms\Components\Password;
use App\Filament\Resources\DriverResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->form([
                    Password::make("current_password")
                        ->required()
                        ->password()
                        ->rule("current_password")
                        ->placeholder('••••••••')
                        ->disableAutocomplete(),
                ])->modalWidth('md'),
        ];
    }
}
