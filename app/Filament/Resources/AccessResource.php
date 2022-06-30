<?php

namespace App\Filament\Resources;

use App\Enums\Models\AccessStatus;
use App\Filament\Forms\Components\RangeSlider;
use App\Filament\Resources\AccessResource\Pages;
use App\Filament\Resources\AccessResource\RelationManagers;
use App\Models\Tenant\Access;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccessResource extends Resource
{
    protected static ?string $model = Access::class;

    protected static ?string $navigationIcon = 'heroicon-o-qrcode';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Fieldset::make('Driver')
                            ->relationship('driver')
                            ->schema([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('phone_number')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                            ]),
                        Forms\Components\Fieldset::make('Vehicle')
                            ->relationship('vehicle')
                            ->schema([
                                Forms\Components\TextInput::make('plate_number')
                                    ->required(),

                                Forms\Components\TextInput::make('brand')
                                    ->required(),

                                Forms\Components\TextInput::make('model')
                                    ->required(),

                                Forms\Components\TextInput::make('color'),
                            ]),

                        Forms\Components\Fieldset::make('Parking Lot')
                            ->schema([
                                Forms\Components\Select::make('parking_lot')
                                    ->label('Name')
                                    ->relationship('parkingLot', 'name')->columnSpan('full'),
                            ]),

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
                            ->hint('Time in minutes (min)')
                            ->helperText('<span class="text-xs"><span class="text-sm">&#9432;</span> The Access will be deactivated if it is not used by the client after the timeout.</span>'),

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
