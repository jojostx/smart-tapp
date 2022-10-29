<?php

namespace App\Filament\Resources;

use App\Enums\Models\ParkingLotStatus;
use App\Filament\Forms\Components\Password;
use App\Filament\Forms\Components\Qrcode;
use App\Filament\Resources\ParkingLotResource\Pages;
use App\Filament\Resources\ParkingLotResource\RelationManagers;
use App\Models\Tenant\ParkingLot;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParkingLotResource extends Resource
{
    protected static ?string $model = ParkingLot::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-boards';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(124)
                            ->minLength(2)
                            ->rules(['alpha_dash'])
                            ->unique(ignoreRecord: true),
                        Forms\Components\Radio::make('status')
                            ->options([
                                'open' => 'Open',
                                'closed' => 'Closed'
                            ])
                            ->enum(ParkingLotStatus::class)
                            ->default(ParkingLotStatus::OPEN)
                            ->descriptions(ParkingLotStatus::toDescriptionArray())
                            ->columnSpan('full')
                            ->required(),
                    ])->columnSpan([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3,
                    ]),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Fieldset::make('Qrcode')
                            ->schema([
                                Qrcode::make('qrcode')
                                    ->disableLabel()
                                    ->helperText('<span class="text-xs"><span class="text-sm">&#9432;</span> The Qrcode will have dimensions: [500 X 500] pixels.</span>')
                                    ->columnSpan('full')
                                    ->content(fn (?ParkingLot $record) => $record ? $record->qrcode : '-')
                                    ->downloadName(fn (?ParkingLot $record) => $record ? $record->name : ''),
                            ])
                            ->columnSpan(1)
                            ->hiddenOn(Pages\CreateParkingLot::class),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Modified at')
                                    ->content(fn (?ParkingLot $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn (?ParkingLot $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                            ])->columnSpan(1)->columns(1)
                    ])->columns(2)
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 4,
                    ]),
            ])->columns([
                'sm' => 1,
                'md' => 5,
                'lg' => 7,
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
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Modified at')->date(config('filament.date_format'))->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date(config('filament.date_format'))->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ParkingLotStatus::toArray())
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->form([
                            Password::make("current_password")
                                ->required()
                                ->password()
                                ->rule("current_password")
                                ->disableAutocomplete(),
                        ]),
                ])
                    ->icon('heroicon-o-dots-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->form([
                        Password::make("current_password")
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
            'index' => Pages\ListParkingLots::route('/'),
            'create' => Pages\CreateParkingLot::route('/create'),
            'edit' => Pages\EditParkingLot::route('/{record}/edit'),
        ];
    }
}
