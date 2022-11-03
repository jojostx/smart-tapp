<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\Models\ParkingLotStatus;
use App\Filament\Notifications\Notification;
use App\Filament\Traits\WithCurrentPasswordField;
use App\Models\Tenant\Administration;
use App\Models\Tenant\ParkingLot;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Position;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class ParkingLotsRelationManager extends RelationManager
{
    use WithCurrentPasswordField;

    protected static ?string $title = "Assigned Parking Lots";
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $inverseRelationship = 'administrators';
    protected static string $relationship = 'parkingLots';

    protected function getTableDescription(): string | Htmlable | null
    {
        return str('<span class="text-sm">
            This table shows all the Parking lot assigned to this admin. <br>
            It also show when the assignment will expire.</span>')
            ->toHtmlString();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('status')
                    ->enum(ParkingLotStatus::toArray())
                    ->colors([
                        'warning' => fn ($state): bool => $state === ParkingLotStatus::CLOSED->value,
                        'success' => fn ($state): bool => $state === ParkingLotStatus::OPEN->value,
                        'danger' => fn ($state): bool => $state === ParkingLotStatus::FILLED->value,
                    ]),
                Tables\Columns\TextColumn::make('name')
                    ->label('Parking lot name'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->date(config('filament.date_format'))
                    ->description(function (Tables\Columns\TextColumn $column) {
                        if (today()->isSameDay($column->getState())) {
                            return str('<div class="text-xs filament-badge-danger"><span>Expired</span></div>')
                                ->toHtmlString();
                        }
                        return null;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('administrators.updated_at')
                    ->label('assigned at')
                    ->date(config('filament.date_format'))
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->modalSubheading('Attach a parking lot to be administered by this admin.')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->placeholder('Select a Parking Lot')
                            ->reactive(),
                        Forms\Components\Toggle::make('disable_expiry')
                            ->reactive()
                            ->label('Disable expiry')
                            ->helperText('Click to disable the expiration of the administrative privilege. '),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->hidden(fn (callable $get) => (bool) $get('disable_expiry'))
                            ->rules(['exclude_if:disable_expiry,true', 'required'])
                            ->minDate(fn () => Carbon::now()->addHours(9))
                            ->placeholder('Select a date')
                            ->withoutSeconds()
                            ->helperText('Administrative privilege will expire after your specified period'),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\DetachAction::make()
                        ->button()
                        ->form([
                            static::getCurrentPasswordField(),
                        ]),
                    Tables\Actions\Action::make('expire')
                        ->button()
                        ->hidden(function (ParkingLot $record) {
                            /** @var \App\Models\Tenant\Administration|null */
                            $administration = Administration::withExpired()->where([
                                ['user_id', '=', $record->administration->user_id],
                                ['parking_lot_id', '=', $record->id],
                            ])->first();

                            return $administration?->isExpired();
                        })
                        ->color('warning')
                        ->icon('heroicon-o-pause')
                        ->requiresConfirmation()
                        ->modalHeading('Expire Administrative Privilege')
                        ->modalSubheading('This will disable the administrative privilege of the
                         admin over this parking lot and prevent them from administering the parking lot.')
                        ->action(function (ParkingLot $record) {
                            /** @var \App\Models\Tenant\Administration|null */
                            $administration = Administration::withExpired()->where([
                                ['user_id', '=', $record->administration->user_id],
                                ['parking_lot_id', '=', $record->id],
                            ])->first();

                            if (filled($administration) && $administration->expire()) {
                                Notification::make('expired_success')
                                    ->title('Expiration successful')
                                    ->body('The Administrative privilege was successfully expired.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make('expired_failed')
                                    ->title('Expiration failed')
                                    ->body('Unable to expire the Administrative privilege. Please try again later.')
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('extend-expiry')
                        ->button()
                        ->color('primary')
                        ->icon('heroicon-o-play')
                        ->requiresConfirmation()
                        ->modalWidth('md')
                        ->form([
                            Forms\Components\Toggle::make('disable_expiry')
                                ->reactive()
                                ->label('Disable expiry')
                                ->helperText('Click to disable the expiration of the administrative privilege. '),
                            Forms\Components\DateTimePicker::make('expires_at')
                                ->hidden(fn (callable $get) => (bool) $get('disable_expiry'))
                                ->rules(['exclude_if:disable_expiry,true', 'required'])
                                ->minDate(fn () => Carbon::now()->addHours(9))
                                ->placeholder('Select a date')
                                ->withoutSeconds()
                                ->helperText('Administrative privilege will expire after your specified period'),
                        ])
                        ->action(function (ParkingLot $record, array $data) {
                            /** @var \App\Models\Tenant\Administration|null */
                            $administration = Administration::withExpired()->where([
                                ['user_id', '=', $record->administration->user_id],
                                ['parking_lot_id', '=', $record->id],
                            ])->first();

                            if (!$administration) {
                                Notification::make('revive_failed')
                                    ->title('Failed to extend Expiration')
                                    ->body('Unable to extend the Administrative privilege. Please try again later.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $disable_expiry = $data['disable_expiry'] ?? true;
                            if (!$disable_expiry && !empty($data['expires_at'])) {
                                try {
                                    $administration->expiresAt(Carbon::parse($data['expires_at']));
                                    $extended = $administration->save();
                                } catch (\Throwable $th) {
                                    $extended = false;
                                }
                            } else {
                                $extended = $administration->makeEternal();
                            }

                            if ($extended) {
                                Notification::make('revive_success')
                                    ->title('Expiration Extended successfully')
                                    ->body('The Administrative privilege was successfully extended.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make('revive_failed')
                                    ->title('Failed to extend Expiration')
                                    ->body('Unable to extend the Administrative privilege. Please try again later.')
                                    ->danger()
                                    ->send();
                            }
                        })
                ])->icon('heroicon-o-dots-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ])
            ->actionsPosition(Position::BeforeCells);
    }
}
