<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers\DriversRelationManager;
use App\Filament\Traits\WithCurrentPasswordField;
use App\Models\Tenant\Vehicle;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class VehicleResource extends Resource
{
    use WithCurrentPasswordField;

    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function getGloballySearchableAttributes(): array
    {
        return ['plate_number', 'brand', 'model'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Plate Number' => $record->plate_number,
            'Model' => $record->model ?? '-',
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
                                Forms\Components\TextInput::make('plate_number')
                                    ->required(),
                                Forms\Components\TextInput::make('brand')
                                    ->maxValue(125)
                                    ->required(),
                                Forms\Components\TextInput::make('model')
                                    ->maxValue(125)
                                    ->required(),
                                Forms\Components\TextInput::make('color')
                                    ->maxValue(20),
                            ]),
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (?Vehicle $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?Vehicle $record): string => $record ? $record->created_at->diffForHumans() : '-'),
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
                Tables\Columns\TextColumn::make('plate_number')
                    ->label('Plate Number')
                    ->formatStateUsing(fn ($state) => str($state)->upper())
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')->searchable(),
                Tables\Columns\TextColumn::make('model')->searchable(),
                Tables\Columns\TextColumn::make('color')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')->label('Modified at')->date(config('filament.date_format'))->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date(config('filament.date_format'))->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            DriversRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
