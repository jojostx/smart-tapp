<?php

namespace App\Filament\Components\Settings;

use App\Models\CreditCard;
use App\Models\Tenant;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Position;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use Livewire\Component;

class PaymentMethods extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use WithRateLimiting;

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
            Tables\Columns\BadgeColumn::make('status')
                ->getStateUsing(function (CreditCard $record) {
                    if ($record->isDisabled()) {
                        return 'disabled';
                    }

                    if ($record->isDefault() && $record->isEnabled()) {
                        return 'default';
                    }

                    return '';
                })
                ->enum([
                    'default' => 'Default method',
                    'disabled' => 'Disabled',
                ])
                ->colors([
                    'success',
                    'danger' => static fn ($state): bool => $state === 'disabled',
                ]),
            Tables\Columns\TextColumn::make('card_number')
                ->getStateUsing(fn ($record) => "$record->first_6******$record->last_4")
                ->size('sm'),
            Tables\Columns\TextColumn::make('issuer')->size('sm'),
            Tables\Columns\TextColumn::make('type')->icon('jojoicon-o-mastercard'),
            Tables\Columns\TextColumn::make('created_at')->date(config('filament.date_format')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Tables\Actions\Action::make('make-default')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading(function (): string {
                        return 'Make default';
                    })
                    ->modalSubheading('This will make this card the default payment method for all subscription related charges.')
                    ->action(function (CreditCard $record) {
                        if ($record->isExpired()) {
                            Notification::make('make-default-error')
                                ->title('Expired Credit Card can not be marked as default')
                                ->danger()
                                ->send();

                            return;
                        }

                        if ($record->makeDefault() && $record->isDefault()) {
                            Notification::make('make-default-success')
                                ->title('Credit Card successfully marked as default')
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn (CreditCard $record) => $record->isEnabled() && $record->isNotExpired() && $record->isNotDefault()),

                Tables\Actions\Action::make('enable-card')
                    ->color('primary')
                    ->icon('heroicon-o-play')
                    ->requiresConfirmation()
                    ->modalHeading(function (): string {
                        return 'enable card';
                    })
                    ->modalSubheading('This will enable the card and allow it to be used for subscription related charges.')
                    ->action(function (CreditCard $record) {
                        if ($record->enable()) {
                            Notification::make('enable-card')
                                ->title('Credit Card successfully enabled')
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn (CreditCard $record) => $record->isDisabled()),

                Tables\Actions\Action::make('disable-card')
                    ->color('primary')
                    ->icon('heroicon-o-pause')
                    ->requiresConfirmation()
                    ->modalHeading(function (): string {
                        return 'disable card';
                    })
                    ->modalSubheading('This will disable the card and prevent it from being used as a fallback option for subscription related charges.')
                    ->action(function (CreditCard $record) {
                        if ($record->disable()) {
                            Notification::make('disable-card')
                                ->title('Credit Card successfully disabled')
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn (CreditCard $record) => $record->isEnabled()),

                Tables\Actions\Action::make('delete-card')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalSubheading('Are you sure you want to do this? This will permanently delete the card.')
                    ->action(function (CreditCard $record) {
                        if ($record->delete()) {
                            Notification::make('delete-card')
                                ->title('Credit Card successfully deleted')
                                ->success()
                                ->send();
                        }
                    }),
            ])
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('add-new-card')
                ->button()
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->requiresConfirmation()
                ->modalHeading(function (): string {
                    return 'Add New Card';
                })
                ->modalSubheading(function () {
                    $amount = getTokenizationAmount();
                    $currency = \getTokenizationCurrency();

                    return str(
                        "To add a new card you will be redirected to the payment gateway page where you'll be charged a 
                        token of <span class='text-sm font-semibold'>{$currency} {$amount}<span/>."
                    )->toHtmlString();
                })
                ->action(fn () => $this->addCard()),
        ];
    }

    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No Credit cards yet';
    }

    protected function addCard()
    {
        try {
            $this->rateLimit(5, 3600);

            $tenant = tenant();

            //This generates a payment reference
            $reference = Flutterwave::generateReference();

            // Enter the details of the payment
            $data = [
                'tx_ref' => $reference,
                'payment_options' => 'card',
                'amount' => getTokenizationAmount(),
                'currency' => getTokenizationCurrency(), // remember to used tenant configured currency
                'redirect_url' => route('filament.subscription.card.add'),
                'customer' => [
                    'name' => auth()->user()->name,
                    'email' => $tenant->email,
                    'phone_number' => auth()->user()->phone_number,
                ],
                'meta' => [
                    'tenant' => $tenant->id,
                ],
                'customizations' => [
                    'title' => 'Charge Card',
                ],
            ];

            $payment = Flutterwave::initializePayment($data);

            if ($payment['status'] != 'success') {
                // notify something went wrong
                Notification::make('tokenization_error')
                    ->title('An error occurred while processing your request')
                    ->body($payment['message'])
                    ->danger()
                    ->send();

                return;
            }

            return redirect()->to($payment['data']['link']);
        } catch (TooManyRequestsException $exception) {
            Notification::make('too_many_request')
                ->title('Too many requests')
                ->body("Slow down! Please wait another {$exception->minutesUntilAvailable} minutes.")
                ->danger()
                ->send();
        }
    }

    public function render(): View
    {
        return view(static::$view);
    }
}
