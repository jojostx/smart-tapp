<?php

namespace App\Filament\Resources;

use App\Enums\Models\AccessStatus;
use App\Filament\Forms\Components\RangeSlider;
use App\Filament\Resources\AccessResource\Pages;
use App\Filament\Resources\AccessResource\RelationManagers;
use App\Filament\Traits\canCleanupStaleRecords;
use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class AccessResource extends Resource
{
    use canCleanupStaleRecords;

    protected static ?string $model = Access::class;

    protected static ?string $navigationIcon = 'heroicon-o-qrcode';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected $rules = [];

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
                                // also check if there are dangling vehicle records [vehicle's with no related access and drivers]
                                // Vehicle::doesntHave('drivers')->whereDoesntHave('accesses', function (Builder $query) {
                                //     $query->where('created_at', '<', now()->subDay());
                                // })->delete();
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

                                // also check if there are dangling driver records [driver's with no related access and vehicles]
                                // Driver::doesntHave('vehicles')->whereDoesntHave('accesses', function (Builder $query) {
                                //     $query->where('created_at', '<', now()->subDay());
                                // })->delete();
                                static::cleanupstaleRecords($record, ['vehicles'], 2);

                                $record->fill($data);
                                $record->save();

                                return $record->getKey();
                            })
                            ->unique(table: 'accesses', column: 'driver_id', callback: function (Unique $rule, callable $get) {
                                return $rule->where('vehicle_id',  $get('vehicle_id'));
                            })
                            ->visibleOn(Pages\CreateAccess::class),

                        Forms\Components\Fieldset::make('Vehicle')
                            ->relationship('vehicle')
                            ->schema([
                                Forms\Components\TextInput::make('plate_number')->required()->unique('vehicles', 'plate_number'),
                                Forms\Components\TextInput::make('brand')->required(),
                                Forms\Components\TextInput::make('model')->required(),
                                Forms\Components\TextInput::make('color'),

                            ])
                            ->hiddenOn(Pages\CreateAccess::class),

                        Forms\Components\Fieldset::make('Driver')
                            ->relationship('driver')
                            ->schema([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('phone_number')->required()->unique('drivers', 'phone_number'),
                                Forms\Components\TextInput::make('email')->required()->unique('drivers', 'email')
                            ])
                            ->hiddenOn(Pages\CreateAccess::class),

                        Forms\Components\Select::make('parking_lot_id')
                            ->label('Parking Lot')
                            ->relationship('parkingLot', 'name'),

                        Forms\Components\DatePicker::make('valid_until')
                            ->label('Valid Until')
                            ->minDate(now())
                            ->default(now()->addDay())
                            ->maxDate(now()->addDays(3)),

                        RangeSlider::make('expires_after')
                            ->label('Activation Timeout')
                            ->max(120)
                            ->min(30)
                            ->step(10)
                            ->hint('<span>&#9432;</span> Time in minutes (min)')
                            ->helperText('<span class="text-xs"><span>&#9432;</span> The Access will be deactivated if it is not used by the customer after the timeout.</span>'),

                        Forms\Components\Radio::make('status')
                            ->options(AccessStatus::toArray())->default(AccessStatus::ISSUED)
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
                        'danger' => fn ($state): bool => $state === AccessStatus::ISSUED->value,
                        'success' => fn ($state): bool => $state === AccessStatus::ACTIVE->value,
                    ]),
                Tables\Columns\TextColumn::make('parkingLot.name')->searchable(),
                Tables\Columns\TextColumn::make('driver.name')->searchable(),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn (): string => 'Delete Access')
                    ->modalWidth('md')
                    ->modalSubheading(fn (Access $record): string => "Are you sure you want to delete the Access for the Vehicle [{$record->vehicle->plate_number}] with Driver [{$record->driver->name} - {$record->driver->phone_number}]?")
                    ->form([
                        \Phpsa\FilamentPasswordReveal\Password::make("current_password")
                            ->required()
                            ->password()
                            ->rule("current_password")
                            ->disableAutocomplete(),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->form([
                        \Phpsa\FilamentPasswordReveal\Password::make("current_password")
                            ->required()
                            ->password()
                            ->rule("current_password")
                            ->disableAutocomplete(),
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
