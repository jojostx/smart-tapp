<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Filament\Resources\AccessResource;
use Filament\Forms\Components\TextInput;
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
                    TextInput::make("current_password")
                        ->required()
                        ->password()
                        ->rule("current_password"),
                ]),
        ];
    }
}
