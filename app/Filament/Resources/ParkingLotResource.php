<?php

namespace App\Filament\Resources;

use App\Enums\Models\ParkingLotStatus;
use App\Filament\Resources\ParkingLotResource\Pages;
use App\Filament\Resources\ParkingLotResource\RelationManagers;
use App\Models\Tenant\ParkingLot;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParkingLotResource extends Resource
{
    protected static ?string $model = ParkingLot::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-boards';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->rules(['alpha_dash'])
                            ->unique(),
                        Forms\Components\Radio::make('status')
                            ->options(ParkingLotStatus::toArray())->default(ParkingLotStatus::OPEN)
                            ->descriptions(ParkingLotStatus::toDescriptionArray())->columnSpan('full')
                            ->required(),
                    ])->columnSpan(1),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (?ParkingLot $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?ParkingLot $record): string => $record ? $record->created_at->diffForHumans() : '-')
                    ])
                    ->columnSpan(1),
            ])->columns([
                'sm' => 1,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum([ParkingLotStatus::cases()])
                    ->colors([
                        'warning' => fn ($state): bool => $state === ParkingLotStatus::CLOSED->value,
                        'success' => fn ($state): bool => $state === ParkingLotStatus::OPEN->value,
                    ]),
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
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListParkingLots::route('/'),
            'edit' => Pages\EditParkingLot::route('/{record}/edit'),
        ];
    }
}
