<?php

namespace App\Filament\Actions\Tables;

use Filament\Forms;
use Filament\Tables\Actions\DeleteBulkAction;

class PasswordRequiredDeleteBulkAction extends DeleteBulkAction
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
