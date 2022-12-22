<?php

namespace App\Filament\Components\Settings;

use App\Models\Tenant;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Position;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Jojostx\Larasubs\Models\Subscription;
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
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'primary' => static fn ($state): bool => $state === Subscription::STATUS_TRAILLING,
                    'success' => static fn ($state): bool => $state === Subscription::STATUS_ACTIVE,
                    'warning' => static fn ($state): bool => $state === Subscription::STATUS_OVERDUE,
                    'secondary' => static fn ($state): bool => $state === Subscription::STATUS_ENDED,
                    'danger' => static fn ($state): bool => $state === Subscription::STATUS_CANCELLED,
                ]),
            Tables\Columns\TextColumn::make('name')->label('Id'),
            Tables\Columns\TextColumn::make('plan.name'),
            Tables\Columns\TextColumn::make('starts_at')->label('Started at')->date(config('filament.date_format')),
            Tables\Columns\TextColumn::make('ends_at')->date(config('filament.date_format')),
            Tables\Columns\TextColumn::make('plan_changed_at')->date(config('filament.date_format')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Tables\Actions\Action::make('renew-subscription')
                    ->label('Renew')
                    ->color('success')
                    ->icon('heroicon-o-refresh')
                    ->requiresConfirmation()
                    ->modalHeading(fn (): string => 'Renew Subscription')
                    ->url(\route('filament.plans.checkout', ['tenant' => \tenant()->getTenantKey()]))
                    ->openUrlInNewTab()
                    ->visible(fn (Subscription $record) => $record->hasEnded()),

                Tables\Actions\Action::make('change-plan')
                    ->color('primary')
                    ->icon('heroicon-o-switch-horizontal')
                    ->requiresConfirmation()
                    ->modalSubheading('You will be charged a prorated amount for changing plan depending on the time elapsed on your current plan!')
                    ->url(\route('filament.plans.checkout', ['tenant' => \tenant()->getTenantKey()]))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => tenantCanChangePlanFor($record)),

                Tables\Actions\Action::make('cancel-subscription')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Subscription?')
                    ->modalSubheading('This will cancel the subscription at the end of the period and will disable auto-renewal.')
                    ->action(fn (Subscription $record) => $record->cancel())
                    ->visible(fn (Subscription $record) => $record->notCancelled() && $record->hasNotEnded()),

                Tables\Actions\Action::make('reactivate-subscription')
                    ->label('Reactivate')
                    ->color('warning')
                    ->icon('heroicon-o-play')
                    ->requiresConfirmation()
                    ->modalHeading('Reactivate Subscription?')
                    ->modalSubheading('This will reactivate the subscription and will enable auto-renewal.')
                    ->action(fn (Subscription $record) => $record->reactivate())
                    ->visible(fn (Subscription $record) => $record->isCancelled() && $record->hasNotEnded()),
            ])
        ];
    }

    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
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
