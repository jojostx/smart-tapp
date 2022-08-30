<?php

namespace App\Filament\Resources;

use App\Enums\Models\ReparkRequestStatus;
use App\Filament\Resources\ReparkRequestResource\Pages;
use App\Filament\Resources\ReparkRequestResource\RelationManagers;
use App\Models\Tenant\Access;
use App\Models\Tenant\ReparkRequest;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReparkRequestResource extends Resource
{
    protected static ?string $model = ReparkRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'Parking';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        static::getAccessForReparkRequestSelect('blocker'),
                        static::getAccessForReparkRequestSelect('blockee'),

                        Forms\Components\Checkbox::make('shouldNotify')
                            ->label('Send Notification')
                            ->default(true)
                            ->helperText("If selected, the Repark request notification will be sent to the Blocker")
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?ReparkRequest $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (?ReparkRequest $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                    ])
                    ->extraAttributes(['class' => 'hidden sm:block'])
                    ->columns(2)
                    ->columnSpan(2),
            ])->columns([
                'sm' => 4,
                'lg' => null,
            ]);;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('status')
                    ->enum(ReparkRequestStatus::toArray())
                    ->colors([
                        'danger' => fn ($state): bool => $state === ReparkRequestStatus::UNRESOLVED->value,
                        'success' => fn ($state): bool => $state === ReparkRequestStatus::RESOLVED->value,
                    ]),
                Tables\Columns\TextColumn::make('blockerDriver.phone_number')->searchable(),
                Tables\Columns\TextColumn::make('blockeeDriver.phone_number')->searchable(),
                Tables\Columns\TextColumn::make('blockerVehicle.plate_number')->searchable(),
                Tables\Columns\TextColumn::make('blockeeVehicle.plate_number')->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ReparkRequestStatus::toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && boolval($status = ReparkRequestStatus::tryFrom($data['value']))) {
                            return $query->status($status);
                        }

                        return $query;
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('notify')
                    ->visible(fn (ReparkRequest $record) => !$record->isResolved())
                    ->color('primary')
                    ->tooltip('Notify Driver')
                    ->requiresConfirmation()
                    ->modalHeading(function (): string {
                        return "Notify Driver";
                    })
                    ->modalSubheading('This will mark this request as "resolving" and send a notification (text message) to the Driver of the vehicle that is blocking another!')
                    ->action(function () {
                        \dd('ok');
                    }),

                Tables\Actions\Action::make('resolve')
                    ->visible(fn (ReparkRequest $record) => !$record->isResolved())
                    ->color('danger')
                    ->tooltip('Resolve Repark Request')
                    ->requiresConfirmation()
                    ->modalHeading(function (): string {
                        return "Resolve Repark Request";
                    })
                    ->action(function () {
                        \dd('ok');
                    }),
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

    protected static function getNavigationBadge(): ?string
    {
        $routeBaseName = static::getRouteBaseName();

        if (!request()->routeIs("{$routeBaseName}.*")) {
            return ReparkRequest::unresolved()->count() ?? null;
        }

        return null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReparkRequests::route('/'),
            'create' => Pages\CreateReparkRequest::route('/create'),
        ];
    }

    protected static function getAccessForReparkRequestSelect(string $name = ""): Select
    {
        $name = str($name);

        return Select::make("{$name->lower()->value()}" . "_access_id")
            ->label("{$name->ucfirst()->value()}" . "'s Access")
            ->relationship("{$name->lower()->value()}" . "Access", 'id')
            ->searchable()
            ->placeholder("Select the Blocker's Access")
            ->searchPrompt("Search by Driver's Name, Phone Number and Vehicle Plate Number")
            ->getSearchResultsUsing(
                function (string $search) {
                    /** @var \Illuminate\Database\Eloquent\Collection */
                    $accesses = Access::with(['driver:id,name,phone_number', 'vehicle:id,plate_number'])
                        ->where(function (Builder $query) {
                            $query->notInactive();
                        })
                        ->where(function ($query) use ($search) {
                            $query->whereHas('driver', function (Builder $query) use ($search) {
                                $query->where('phone_number', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%");
                            })->orWhereHas('vehicle',  function (Builder $query) use ($search) {
                                $query->where('plate_number', 'like', "%{$search}%");
                            });
                        })
                        ->limit(50)
                        ->get(['id', 'driver_id', 'vehicle_id']);

                    $formatted = $accesses->mapWithKeys(function (Access $access) {
                        return [$access->id => "{$access->driver->name} / {$access->driver->phone_number} / {$access->vehicle->plate_number}"];
                    });

                    return $formatted;
                }
            )
            ->required();
    }
}
