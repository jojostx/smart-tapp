<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\PhoneNumberInput;
use App\Filament\Resources\DriverResource\Pages;
use App\Filament\Resources\DriverResource\RelationManagers;
use App\Models\Tenant\Driver;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class DriverResource extends Resource
{
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
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('email')->email()->required(),
                                // Forms\Components\TextInput::make('phone_number')->required()->tel()->reactive(),
                                PhoneNumberInput::make('phone_number')->required()->reactive()->allowedCountries(['NG']),
                                Forms\Components\Placeholder::make('location')->label('Location')->content('Nigeria (NGN)'),
                            ]),
                    ])
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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone_number')->searchable(),
                Tables\Columns\TextColumn::make('phone_verified_at')->date(config('filament.date_format'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')->date(config('filament.date_format'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date(config('filament.date_format'))
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDrivers::route('/'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
