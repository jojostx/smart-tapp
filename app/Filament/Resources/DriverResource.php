<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\PhoneNumberInput;
use App\Filament\Resources\DriverResource\Pages;
use App\Filament\Tables\Columns\ActionableTextColumn;
use App\Models\Tenant\Driver;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\DriverResource\RelationManagers;
use AbanoubNassem\FilamentPhoneField\Forms\Components\PhoneInput;
use App\Filament\Traits\WithCurrentPasswordField;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriverResource extends Resource
{
    use WithCurrentPasswordField;

    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-support';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?string $recordTitleAttribute = 'identifierforfilament';

    protected static ?int $navigationSort = 4;

    public static function getGloballySearchableAttributes(): array
    {
        return ['phone_number', 'name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->name,
            'Phone Number' => $record->phone_number,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                self::detailsSection()
                    ->columnSpan([
                        'sm' => 2,
                    ]),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('phone_verified_at')
                            ->label('Phone Number verified at')
                            ->content(fn (?Driver $record): string => $record ? ($record->phone_verified_at?->diffForHumans() ?? '-') : '-'),
                        Forms\Components\Placeholder::make('email_verified_at')
                            ->label('Email verified at')
                            ->content(fn (?Driver $record): string => $record ? ($record->email_verified_at?->diffForHumans() ?? '-') : '-'),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?Driver $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (?Driver $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                    ])
                    ->extraAttributes(['class' => 'hidden sm:block'])
                    ->columns(2)
                    ->columnSpan(2),
            ])->columns([
                'sm' => 4,
                'lg' => null,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ActionableTextColumn::make('name')->animated()->searchable(),
                ActionableTextColumn::make('phone_number')->animated()->searchable(),
                ActionableTextColumn::make('email')
                    ->animated()
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\IconColumn::make('phone_verified_at')
                    ->label('Phone verified')
                    ->boolean()
                    ->default(false)
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email verified')
                    ->boolean()
                    ->default(false)
                    ->toggleable()
                    ->sortable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date(config('filament.date_format'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->nullable(),
                TernaryFilter::make('phone_verified_at')
                    ->nullable(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make('delete')
                        ->requiresConfirmation()
                        ->modalHeading(fn (): string => 'Delete Driver')
                        ->modalSubheading('Are you sure you would like to do this? Deleting the driver will delete all associated Acessess.')
                        ->modalWidth('md')
                        ->form([
                            static::getCurrentPasswordField()
                        ]),
                ])->icon('heroicon-o-dots-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->form([
                        static::getCurrentPasswordField()
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
            'index' => Pages\ListDrivers::route('/'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }

    protected static function detailsSection(): Section
    {
        return Section::make(__('Driver Details'))
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                // PhoneNumberInput::make('phone_number')
                //     ->required()
                //     ->reactive()
                //     ->allowedCountries(['NG']),
                PhoneInput::make('phone_number')
                    ->required()
                    ->reactive()
                    ->initialCountry('NG'),
                Forms\Components\Placeholder::make('location')
                    ->label('Location')
                    ->content('Nigeria (NGN)'),
            ]);
    }
}
