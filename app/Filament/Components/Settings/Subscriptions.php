<?php

namespace App\Filament\Components\Settings;

use App\Models\Tenant;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Subscriptions extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament::components.settings.subscriptions';

    protected function getTableQuery(): Builder
    {
        /** @var ?Tenant */
        $tenant = tenant();

        return $tenant->subscriptions()->getQuery();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\BadgeColumn::make('status')->colors([
                'primary' => static fn ($state): bool => $state === 'trialling',
                'success' => static fn ($state): bool => $state === 'active',
                'warning' => static fn ($state): bool => $state === 'overdue',
                'secondary' => static fn ($state): bool => $state === 'ended',
                'danger' => static fn ($state): bool => $state === 'cancelled',
            ]),
            Tables\Columns\TextColumn::make('name')->label('Id'),
            Tables\Columns\TextColumn::make('plan.name'),
            Tables\Columns\TextColumn::make('starts_at')->label('Started at')->date(config('filament.date_format')),
            Tables\Columns\TextColumn::make('ends_at')->date(config('filament.date_format')),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No Subscription yet';
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public function render(): View
    {
        return view(static::$view);
    }
}
