<?php

namespace App\Filament\Actions;

use Filament\Forms;
use Filament\Tables\Actions\DeleteAction;

class PasswordRequiredDeleteAction extends DeleteAction
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->requiresConfirmation()
      ->modalHeading("Confirm password")
      ->modalSubheading(
        "Please confirm your password to complete this item."
      )
      ->form([
        Forms\Components\TextInput::make("current_password")
          ->required()
          ->password()
          ->rule("current_password"),
      ]);
  }
}