<?php

namespace App\Filament\Components\Settings;

use App\Models\Tenant; 
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PaymentMethods extends Component implements Tables\Contracts\HasTable
{
  use Tables\Concerns\InteractsWithTable;

  protected static string $view = 'filament::components.settings.payment-methods';

  protected function getTableQuery(): Builder
  {
    /** @var ?Tenant */
    $tenant = tenant();

    return $tenant->creditCards()->getQuery();
  }

  protected function getTableColumns(): array
  {
    return [
      Tables\Columns\TextColumn::make('card_number')->getStateUsing(fn ($record) => "$record->first_6******$record->last_4"),
      Tables\Columns\TextColumn::make('issuer'),
      Tables\Columns\TextColumn::make('type')->icon('jojoicon-o-mastercard'),
      Tables\Columns\TextColumn::make('created_at')->date(config('filament.date_format')),
    ];
  }

  protected function getTableEmptyStateHeading(): ?string
  {
    return 'No Credit cards yet';
  }

  public function render(): View
  {
    return view(static::$view);
  }
}
