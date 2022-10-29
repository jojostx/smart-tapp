<?php

namespace App\Filament\Resources;

use App\Enums\Models\ReparkRequestStatus;
use App\Filament\Forms\Components\Password;
use App\Filament\Resources\ReparkRequestResource\Pages;
use App\Filament\Resources\ReparkRequestResource\RelationManagers;
use App\Models\Tenant\Access;
use App\Models\Tenant\ReparkRequest;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

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
                        static::getAccessForReparkRequestSelect('blocker', "blockee"),
                        static::getAccessForReparkRequestSelect('blockee', "blocker"),

                        Forms\Components\Checkbox::make('shouldNotify')
                            ->label('Send Notification')
                            ->default(true)
                            ->helperText("If selected, the Repark Request notification (text message) will be sent to the Driver of the vehicle that is blocking another!"),

                        Forms\Components\Placeholder::make('hasExistingReparkRequest')
                            ->disableLabel()
                            ->visible(function (callable $get) {
                                $blockee_access_id = (string) $get('blockee_access_id');
                                $blocker_access_id = (string) $get('blocker_access_id');

                                if ($blocker_access_id && $blockee_access_id) {
                                    return static::getExistingReparkRequestQuery($blockee_access_id, $blocker_access_id)->exists();
                                }

                                return false;
                            })
                            ->extraAttributes(['class' => 'bg-danger-100 text-danger-800 rounded-md p-4 border'])
                            ->content(function (callable $get) {
                                $blockee_access_id = (string) $get('blockee_access_id');
                                $blocker_access_id = (string) $get('blocker_access_id');

                                if ($blocker_access_id && $blockee_access_id) {
                                    $reparkRequest = static::getExistingReparkRequestQuery($blockee_access_id, $blocker_access_id)->first();

                                    $link = blank($reparkRequest) ? '#' : route('filament.resources.tenant/repark-requests.index', ['tableSearchQuery' => $reparkRequest->uuid]);

                                    return new HtmlString("<span>An unresolved <a class='font-semibold underline' href='$link'>Repark Request</a> already exists for this Blocker and Blockee. Are you sure you want to create another?</span>");
                                }

                                return '';
                            })
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')->label('id')->formatStateUsing(fn ($state) => str($state)->before('-')->value())->searchable(),
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
                            return $query->whereStatus($status);
                        }

                        return $query;
                    })
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('start-resolving')
                        ->label('Start resolving')
                        ->visible(fn (ReparkRequest $record) => !$record->isResolving())
                        ->color('danger')
                        ->icon('heroicon-o-play')
                        ->tooltip('Start resolving Repark Request')
                        ->requiresConfirmation()
                        ->modalHeading(function (): string {
                            return "Start Resolving Repark Request";
                        })
                        ->form([
                            Forms\Components\Checkbox::make('shouldNotify')
                                ->label('Send Repark Request notification')
                                ->helperText('This will send a Repark Request notification (text message) to the Driver of the vehicle that is blocking another!')
                                ->default(true)
                        ])
                        ->action(function (ReparkRequest $record, ?array $data) {
                            $shouldNotify = isset($data['shouldNotify']) && $data['shouldNotify'];

                            if ($record->startResolving()) {
                                $shouldNotify &&
                                    $record->sendReparkRequestResolutionNotification(checkStatusCountdown: 30) &&
                                    Notification::make()
                                    ->title('Started Resolving Repark Request')
                                    ->body($shouldNotify ? "The **Repark Request notification** will be sent to the Driver's phone" : null)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Unable to Start Resolving Repark Request')
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make("resolve")
                        ->label('Resolve')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->tooltip('Resolve Repark Request')
                        ->visible(fn (ReparkRequest $record) => !$record->isResolved())
                        ->requiresConfirmation()
                        ->modalHeading(function (): string {
                            return "Resolve Repark Request";
                        })
                        ->action(function (ReparkRequest $record) {
                            $record->resolve() &&
                                Notification::make()
                                ->title('The Repark Request has been resolved!')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('notify')
                        ->visible(fn (ReparkRequest $record) => !$record->isResolved())
                        ->color('primary')
                        ->tooltip('Notify Driver')
                        ->icon('heroicon-o-paper-airplane')
                        ->requiresConfirmation()
                        ->modalHeading(function (): string {
                            return "Notify Driver";
                        })
                        ->modalSubheading('This will send a Repark Request notification (text message) to the Driver of the vehicle that is blocking another!')
                        ->action(function (ReparkRequest $record) {
                            $record->sendReparkRequestResolutionNotification(checkStatusCountdown: 30) &&
                                Notification::make()
                                ->body("The **Repark Request notification** will be sent to the Driver's phone")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(fn (): string => 'Delete ReparkRequest')
                        ->modalSubheading("Are you sure you want to delete this ReparkRequest?")
                        ->form([
                            Password::make("current_password")
                                ->required()
                                ->password()
                                ->rule("current_password")
                                ->disableAutocomplete(),
                        ]),
                ])->icon('heroicon-o-dots-vertical')
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
            return ReparkRequest::whereUnresolved()->count() ?? null;
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

    protected static function getAccessForReparkRequestSelect(string $name = "", string $dependent_name = ""): Select
    {
        $name = str($name);
        $dependent_name = str($dependent_name);
        $field_name = "{$name->lower()->value()}" . "_access_id";
        $field_label = "{$name->ucfirst()->value()}" . "'s Access";
        $field_relationship_name = "{$name->lower()->value()}" . "Access";

        $dependent_field_name = "{$dependent_name->lower()->value()}" . "_access_id";

        return Select::make($field_name)
            ->label($field_label)
            ->relationship($field_relationship_name, 'id')
            ->placeholder("Select the {$name->ucfirst()->value()}'s Access")
            ->searchPrompt("Search by Driver's Name, Phone Number and Vehicle Plate Number")
            ->searchable()
            ->reactive()
            ->getSearchResultsUsing(
                function (string $search, callable $get) use ($dependent_field_name) {
                    /** @var \Illuminate\Database\Eloquent\Collection */
                    $accesses = Access::with(['driver:id,name,phone_number', 'vehicle:id,plate_number'])
                        ->whereNot("id", $get($dependent_field_name))
                        ->where(function (Builder $query) {
                            $query->whereNotInactive();
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

    protected static function getExistingReparkRequestQuery(string $blockee_access_id = '', string $blocker_access_id = ''): Builder
    {
        return ReparkRequest::whereUnresolved()
            ->where(function (Builder $query) use ($blockee_access_id, $blocker_access_id) {
                $query->where([
                    ['blockee_access_id', '=', $blockee_access_id],
                    ['blocker_access_id', '=', $blocker_access_id],
                ])->orWhere(function (Builder $query) use ($blockee_access_id, $blocker_access_id) {
                    $query->where([
                        ['blockee_access_id', '=', $blocker_access_id],
                        ['blocker_access_id', '=', $blockee_access_id],
                    ]);
                });
            })->latest();
    }
}
