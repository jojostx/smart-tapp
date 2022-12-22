<?php

namespace App\Filament\Traits;

use App\Filament\Forms\Components\Password;

trait WithCurrentPasswordField
{
    public static function getCurrentPasswordField()
    {
        return Password::make('current_password')
            ->required()
            ->password()
            ->currentPassword()
            ->placeholder('••••••••')
            ->disableAutocomplete();
    }
}
