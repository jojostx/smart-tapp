<?php

namespace App\Filament\Components\Settings;

use App\Models\Receipt;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PaymentReceipts extends Component implements Tables\Contracts\HasTable
{
  use Tables\Concerns\InteractsWithTable;

  protected static string $view = 'filament::components.settings.payment-receipts';

  protected function getTableQuery(): Builder
  {
    /** @var ?Tenant */
    $tenant = tenant();

    return $tenant->receipts()->getQuery();
  }

  protected function getTableColumns(): array
  {
    return [
      Tables\Columns\TextColumn::make('amount')->money(fn ($record) => $record->currency)
        ->formatStateUsing(static::getFormattedAmount()),
      Tables\Columns\TextColumn::make('organization'),
      Tables\Columns\TextColumn::make('name'),
      Tables\Columns\TextColumn::make('email'),
      Tables\Columns\TextColumn::make('tax_number'),
      Tables\Columns\TextColumn::make('address')->wrap()->toggleable()->toggledHiddenByDefault(),
      Tables\Columns\TextColumn::make('zip_code')->wrap()->toggleable()->toggledHiddenByDefault(),
      Tables\Columns\TextColumn::make('created_at')->label('Paid at')->date(config('filament.date_format')),
    ];
  }

  protected function getTableEmptyStateHeading(): ?string
  {
    return 'No Receipts yet';
  }

  public static function getFormattedAmount()
  {
    return fn (Receipt $record, int|string $state) =>\currency($record->currency)->getSymbol() . \number_format($state);
  }

  public function render(): View
  {
    return view(static::$view);
  }
}
