<?php

namespace App\Filament\Resources;

use App\Enums\Models\ParkingLotStatus;
use App\Filament\Forms\Components\Qrcode;
use App\Filament\Resources\ParkingLotResource\Pages;
use App\Filament\Resources\ParkingLotResource\Widgets;
use App\Filament\Traits\WithCurrentPasswordField;
use App\Models\Tenant\ParkingLot;
use App\Models\Tenant\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;

class ParkingLotResource extends Resource
{
    use WithCurrentPasswordField;

    protected static ?string $model = ParkingLot::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-boards';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 5;

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
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Modified at')
                            ->content(fn (?ParkingLot $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?ParkingLot $record): string => $record ? $record->created_at->diffForHumans() : '-'),
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
                    ->options(ParkingLotStatus::toArray()),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->form([
                            static::getCurrentPasswordField(),
                        ]),
                ])
                    ->icon('heroicon-o-dots-vertical'),
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
            'index' => Pages\ListParkingLots::route('/'),
            'create' => Pages\CreateParkingLot::route('/create'),
            'edit' => Pages\EditParkingLot::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\QrcodeWidget::class,
        ];
    }

    protected static function detailsSection(): Section
    {
        return Section::make(__('User Details'))
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(124)
                    ->minLength(2)
                    ->rules(['alpha_dash'])
                    ->unique(ignoreRecord: true)
                    ->visible(function (ParkingLot $record) {
                        /** @var User */
                        $user = Filament::auth()->user();

                        return $user->isSuperAdmin();
                    }),
                Forms\Components\Radio::make('status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ])
                    ->enum(ParkingLotStatus::class)
                    ->default(ParkingLotStatus::OPEN)
                    ->descriptions(ParkingLotStatus::toDescriptionArray())
                    ->columnSpan('full')
                    ->required(),
            ]);
    }
}
