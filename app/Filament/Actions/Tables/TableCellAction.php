<?php

namespace App\Filament\Actions\Tables;

use Filament\Support\Actions\Concerns\HasKeyBindings;
use Filament\Tables\Actions\Action;

class TableCellAction extends Action
{
  use HasKeyBindings;

  protected string $view = 'filament::actions.tables.table-cell-action';
}
