<?php

namespace App\Filament\Resources;

use App\Enums\Models\AccessStatus;
use App\Filament\Actions\Tables\CopyAction;
use App\Filament\Forms\Components\RangeSlider;
use App\Filament\Resources\AccessResource\Pages;
use App\Filament\Resources\AccessResource\RelationManagers;
use App\Filament\Traits\CanCleanupStaleRecords;
use App\Filament\Traits\WithCurrentPasswordField;
use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Unique;
use Route;

class AccessResource extends Resource
{
    use CanCleanupStaleRecords;
    use WithCurrentPasswordField;

    protected static ?string $model = Access::class;

    protected static ?string $navigationIcon = 'heroicon-o-qrcode';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['driver.phone_number', 'vehicle.plate_number'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Driver' => $record->driver->phone_number,
            'Vehicle' => $record->vehicle->plate_number,
        ];
    }

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['driver', 'vehicle']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->relationship('vehicle', 'plate_number')
                            ->placeholder("Type in the Vehicle's Plate Number to select a Vehicle or add a new vehicle by clicking the (+) button")
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => Vehicle::where('plate_number', 'like', "%{$search}%")->limit(50)->pluck('plate_number', 'id'))
                            ->getOptionLabelUsing(fn ($value): ?string => Vehicle::find($value)?->plate_number)
                            ->createOptionForm(function () {
                                return Form::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('plate_number')
                                            ->placeholder('ex: ABG-VFF32')
                                            ->hint("The Vehicle's plate number")
                                            ->required()
                                            ->unique('vehicles', 'plate_number'),
                                        Forms\Components\TextInput::make('brand')
                                            ->placeholder('ex: Toyota')
                                            ->required(),
                                        Forms\Components\TextInput::make('model')
                                            ->placeholder('ex: Camry')
                                            ->required(),
                                        Forms\Components\TextInput::make('color')
                                            ->placeholder('ex: Blue'),
                                    ])
                                    ->columns([
                                        'sm' => 1,
                                        'md' => 2
                                    ])
                                    ->getSchema();
                            })
                            ->createOptionAction(fn ($action) => $action->modalHeading(__('Register A New Vehicle')))
                            ->createOptionUsing(function (Select $component, array $data, callable $set, callable $get) {
                                // delete previously created model before creating another one,
                                if ($get('vehicle_id')) {
                                    Driver::where('id', '=', $get('vehicle_id'))->delete() && $set('vehicle_id', null);
                                }

                                $record = $component->getRelationship()->getRelated();

                                static::cleanupstaleRecords($record, ['drivers'], 2);

                                $record->fill($data);
                                $record->save();

                                return $record->getKey();
                            })
                            ->unique(table: 'accesses', column: 'vehicle_id', callback: function (Unique $rule, callable $get) {
                                return $rule->where('driver_id',  $get('driver_id'));
                            })
                            ->visibleOn(Pages\CreateAccess::class),

                        Forms\Components\Select::make('driver_id')
                            ->relationship('driver', 'phone_number')
                            ->placeholder("Type in the Driver's Phone Number to select a Driver or add a new driver by clicking the (+) button")
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => Driver::where('phone_number', 'like', "%{$search}%")->limit(50)->pluck('phone_number', 'id'))
                            ->createOptionForm(function () {
                                return Form::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->placeholder('ex: John Doe')
                                            ->required(),
                                        Forms\Components\TextInput::make('phone_number')
                                            ->placeholder('ex: +234 8034 062 460')
                                            ->hint("The Driver's phone number")
                                            ->required()
                                            ->unique('drivers', 'phone_number'),
                                        Forms\Components\TextInput::make('email')
                                            ->placeholder('ex: JohnDoe@gmail.com')
                                            ->hint("The Driver's email (optional)")
                                            ->unique('drivers', 'email')
                                            ->dehydrateStateUsing(fn ($state) => str($state)->lower())
                                    ])
                                    ->columns([
                                        'sm' => 1,
                                        'md' => 2
                                    ])
                                    ->getSchema();
                            })
                            ->createOptionAction(fn ($action) => $action->modalHeading(__('Register A New Driver')))
                            ->createOptionUsing(function (Select $component, array $data, callable $set, callable $get) {
                                // delete previously created model before creating another one,
                                if ($get('driver_id')) {
                                    Driver::where('id', '=', $get('driver_id'))->delete() && $set('driver_id', null);
                                }

                                $record = $component->getRelationship()->getRelated();

                                static::cleanupstaleRecords($record, ['vehicles'], 2);

                                $record->fill($data);
                                $record->save();

                                return $record->getKey();
                            })
                            ->unique(
                                table: 'accesses',
                                column: 'driver_id',
                                callback: function (Unique $rule, callable $get) {
                                    return $rule->where('vehicle_id',  $get('vehicle_id'));
                                }
                            )
                            ->visibleOn(Pages\CreateAccess::class),

                        Forms\Components\Fieldset::make('Vehicle')
                            ->relationship('vehicle')
                            ->schema([
                                Forms\Components\TextInput::make('plate_number')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('brand')
                                    ->required(),
                                Forms\Components\TextInput::make('model')
                                    ->required(),
                                Forms\Components\TextInput::make('color'),

                            ])
                            ->hiddenOn(Pages\CreateAccess::class),

                        Forms\Components\Fieldset::make('Driver')
                            ->relationship('driver')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('phone_number')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                            ])
                            ->hiddenOn(Pages\CreateAccess::class),

                        Forms\Components\Select::make('parking_lot_id')
                            ->label('Parking Lot')
                            ->relationship('parkingLot', 'name'),

                        RangeSlider::make('validity_period')
                            ->label('Valid for')
                            ->max(5)
                            ->rule('integer')
                            ->min(1)
                            ->step(1)
                            ->hint('<span>&#9432;</span> Time in days')
                            ->helperText('<span class="text-sm"><span>&#9432;</span> The Access will be deactivated after the validity period.</span>'),

                        // hide if on edit page and access is active or deactivated
                        RangeSlider::make('expiry_period')
                            ->label('Activation Timeout')
                            ->afterStateHydrated(function ($component, $state) {
                                $component->state($state != 0 ? $state : 30);
                            })
                            ->hidden(function (callable $get) {
                                $status = \is_string($get('status')) ? AccessStatus::tryFrom($get('status')) : $get('status');

                                return $status == AccessStatus::INACTIVE ||
                                    $status == AccessStatus::ACTIVE;
                            })
                            ->step(10)
                            ->rule('integer')
                            ->min(30)
                            ->max(120)
                            ->hint('<span>&#9432;</span> Time in minutes')
                            ->helperText('<span class="text-sm"><span>&#9432;</span> The Access will expire if it is not activated by the customer before the timeout.</span>'),

                        Forms\Components\Radio::make('status')
                            ->reactive()
                            ->options(AccessStatus::toArray(['expired']))
                            ->default(AccessStatus::ISSUED)
                            ->rule(new Enum(AccessStatus::class))
                            ->descriptions(AccessStatus::toDescriptionArray())->columnSpan('full'),
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('issued_at')
                            ->label('Issued at')
                            ->content(fn (?Access $record): string => $record ? $record->issued_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (?Access $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?Access $record): string => $record ? $record->created_at->diffForHumans() : '-')
                    ])
                    ->columnSpan(1),
            ])->columns([
                'sm' => 3,
                'lg' => null,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('status')
                    ->enum(AccessStatus::toArray())
                    ->colors([
                        'warning' => fn ($state): bool => $state === AccessStatus::INACTIVE->value,
                        'primary' => fn ($state): bool => $state === AccessStatus::ISSUED->value,
                        'success' => fn ($state): bool => $state === AccessStatus::ACTIVE->value,
                        'danger' => fn ($state): bool => $state === AccessStatus::EXPIRED->value,
                    ]),
                Tables\Columns\TextColumn::make('parkingLot.name')->searchable(),
                Tables\Columns\TextColumn::make('driver.phone_number')->searchable(),
                Tables\Columns\TextColumn::make('vehicle.plate_number')->searchable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->date(config('filament.date_format'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date(config('filament.date_format'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date(config('filament.date_format'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AccessStatus::toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && boolval($status = AccessStatus::tryFrom($data['value']))) {
                            return $query->whereStatus($status);
                        }

                        return $query;
                    })
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('issue')
                        ->label('Issue Activation')
                        ->visible(fn (Access $record) => $record->isExpired() || $record->isInactive())
                        ->color('warning')
                        ->icon('heroicon-o-arrow-circle-up')
                        ->size('lg')
                        ->tooltip('Issue Access Activation')
                        ->requiresConfirmation()
                        ->modalHeading(function (): string {
                            return "Issue Access Activation";
                        })
                        ->modalSubheading(function (): string {
                            return "This will re-issue the access and by default, the Access Activation Notification will be sent to the Driver's phone number.";
                        })
                        ->form([
                            Forms\Components\Checkbox::make('shouldNotify')
                                ->label('Send Activation Notification')
                                ->default(true)
                        ])
                        ->action(function (Access $record, ?array $data) {
                            $shouldNotify = isset($data['shouldNotify']) && $data['shouldNotify'];

                            if ($record->issue()) {
                                $shouldNotify &&
                                    $record->sendAccessActivationNotification(checkStatusCountdown: 30);

                                Notification::make()
                                    ->title('Access Issued Successfully')
                                    ->body($shouldNotify ? "The **Access Activation Notification** will be sent to the Driver's phone" : null)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Unable to Issue Access')
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('send')
                        ->label('Send Activation')
                        ->visible(fn (Access $record) => $record->isIssued() || $record->isActive())
                        ->color('primary')
                        ->icon('heroicon-o-paper-airplane')
                        ->size('lg')
                        ->tooltip('Send Activation Notification')
                        ->requiresConfirmation()
                        ->modalHeading(function (): string {
                            return "Send Activation Notification";
                        })
                        ->modalSubheading(function (): string {
                            return "This will send the Access Activation Notification to the Driver's phone.";
                        })
                        ->form([
                            static::getCurrentPasswordField(),
                        ])
                        ->action(function (Access $record) {
                            $record->sendAccessActivationNotification(checkStatusCountdown: 30) &&
                                Notification::make()
                                ->body("The **Access Activation Notification** will be sent to the Driver's phone")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(fn (): string => 'Delete Access')
                        ->modalWidth('md')
                        ->modalSubheading(fn (Access $record): string => "Are you sure you want to delete the Access for the Vehicle [{$record->vehicle->plate_number}] with Driver [{$record->driver->name} - {$record->driver->phone_number}]?")
                        ->form([
                            static::getCurrentPasswordField(),
                        ]),
                ])
                    ->icon('heroicon-o-dots-vertical'),

                Tables\Actions\Action::make('Activate')
                    ->visible(fn (Access $record) => $record->isExpired() || $record->isInactive() || $record->isIssued())
                    ->color('primary')
                    ->tooltip('Activate Access')
                    ->requiresConfirmation(fn (Access $record) => !$record->isActive())
                    ->modalHeading(function (): string {
                        return "Activate Access";
                    })
                    ->modalSubheading(function (Access $record): string {
                        $a = ($record->isExpired() || $record->isIssued()) ? 'not' : '';
                        return "This will activate the Access and allow the Driver to access their dashboard. By default, the Access Activation Notification will {$a} be sent to the Driver's phone number.";
                    })
                    ->form([
                        Forms\Components\Checkbox::make('shouldNotify')
                            ->label('Send Activation Notification')
                            ->default(fn (Access $record) => $record->isInactive() ? true : false)
                    ])
                    ->action(function (Access $record, ?array $data) {
                        $anotherActiveAccessExists = Access::query()
                            ->whereNotInactive()
                            ->whereRelation('vehicle', 'plate_number', $record->vehicle->plate_number)
                            ->exists();

                        if ($anotherActiveAccessExists) {
                            Notification::make()
                                ->body('Unable to activate because another Access already exists and has been issued for this Vehicle.')
                                ->danger()
                                ->send()
                                ->seconds(30);

                            return;
                        }

                        $shouldNotify = isset($data['shouldNotify']) && $data['shouldNotify'];

                        if ($record->activate()) {
                            $shouldNotify && $record->sendAccessActivationNotification(checkStatusCountdown: 30);

                            Notification::make()
                                ->title('Access Activated Successfully')
                                ->body($shouldNotify ? "The **Access Activation Notification** will be sent to the Driver's phone" : null)
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Unable to Activate Access')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('Deactivate')
                    ->visible(fn (Access $record) => $record->isExpired() || $record->isActive() || $record->isIssued())
                    ->color('danger')
                    ->tooltip('Deactivate Access')
                    ->requiresConfirmation()
                    ->modalHeading(function (): string {
                        return "Deactivate Access";
                    })
                    ->modalSubheading(function (): string {
                        return "This will deactivate the Access and prevent the Driver from accessing their dashboard.";
                    })
                    ->form([
                        static::getCurrentPasswordField(),
                    ])
                    ->action(function (Access $record) {
                        $record->deactivate() &&
                            Notification::make()
                            ->title('Access Deactivated Successfully')
                            ->success()
                            ->send();
                    }),

                CopyAction::make()
                    ->content(fn (Access $record) => $record->activation_link)
                    ->tooltip('Copy Activation link')
                    ->iconButton()
                    ->visible(fn (Access $record) => $record->isExpired() || $record->isActive() || $record->isIssued()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->form([
                        static::getCurrentPasswordField(),
                    ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccesses::route('/'),
            'create' => Pages\CreateAccess::route('/create'),
            'edit' => Pages\EditAccess::route('/{record}/edit'),
        ];
    }
}
