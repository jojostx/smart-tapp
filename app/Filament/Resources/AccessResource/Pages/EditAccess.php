<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Filament\Forms\Components\Password;
use App\Filament\Resources\AccessResource;
use Filament\Forms\Components\Checkbox;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccess extends EditRecord
{
    protected static string $resource = AccessResource::class;

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
                ])
                ->modalWidth('md'),
        ];
    }
}
