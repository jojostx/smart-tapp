<?php

namespace App\Filament\Resources;

use App\Enums\Models\UserAccountStatus;
use App\Enums\Roles\UserRole;
use App\Filament\Forms\Components\HelpCard;
use App\Filament\Forms\Components\SingleOptionMultiSelect;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Forms\Components\Password as FilamentPasswordRevealPassword;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\ParkingLotsRelationManager;
use App\Models\Tenant\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'User Management';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                self::detailsSection()
                    ->columnSpan([
                        'sm' => 2,
                    ]),

                HelpCard::make('user-creation-help')
                    ->content(
                        str(
                            '
                            <ul class="space-y-4 text-primary-800">
                                <li>
                                    <span>• You can assign multiple available Parking lot per admin user.</span>
                                </li>
                                <li>
                                    <span>• After creating an Admin User an email containing instructions to complete the account creation will be sent to their email address.</span>
                                </li>
                                <li>
                                    <span>• The Admin user will have access to the dashboard for administering the Parking lots assigned to them.</span>
                                </li>
                            </ul>'
                        )->toHtmlString()
                    )
                    ->visibleOn([
                        CreateUser::class,
                    ])
                    ->columnSpan(2),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('email_verified_at')
                            ->label('Email verified at')
                            ->content(fn (?User $record): string => $record ? ($record->email_verified_at?->diffForHumans() ?? '-') : '-'),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?User $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (?User $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                    ])
                    ->visibleOn([
                        EditUser::class,
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
                Tables\Columns\TagsColumn::make('roles.name')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum(UserAccountStatus::toArray())
                    ->colors([
                        'danger' => fn ($state): bool => $state === UserAccountStatus::INACTIVE->value,
                        'success' => fn ($state): bool => $state === UserAccountStatus::ACTIVE->value,
                        'warning' => fn ($state): bool => $state === UserAccountStatus::DEACTIVATED->value,
                    ]),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label('Email verified')
                    ->default(false)
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('phone_verified_at')
                    ->boolean()
                    ->label('Phone verified')
                    ->default(false)
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
                TernaryFilter::make('email_verified_at')
                    ->label('Verified Email')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn (User $record) => $record->isActive() ? 'Deactivate' : 'Activate')
                        ->icon('heroicon-s-lock-closed')
                        ->action(fn (User $record) => $record->isActive() ? $record->deactivateAccount() : $record->activateAccount())
                        ->color(fn (User $record) => $record->isActive() ? 'danger' : 'primary')
                        ->visible(fn (User $record) => $record->hasActivatedAccount()),
                ])->icon('heroicon-o-dots-vertical'),
            ])
            ->bulkActions([]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->name,
            'Email' => $record->email,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role(Role::query()->whereNot('name', UserRole::SUPER_ADMIN)->get(['id']))->with('roles');
    }

    public static function getRelations(): array
    {
        return [
            ParkingLotsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    protected static function detailsSection(): Section
    {
        return Section::make(__('User Details'))
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->placeholder('ex: John Doe')
                    ->validationAttribute(__('Name'))
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->placeholder('ex: example@gmail.com')
                    ->validationAttribute(__('Email'))
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->email(),

                Forms\Components\TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->required()
                    ->reactive()
                    ->unique('users', 'phone_number', ignoreRecord: true)
                    ->placeholder('ex: 09035055833')
                    ->tel()
                    ->rule(Rule::phone()->country(['NG'])),

                SingleOptionMultiSelect::make('role')
                    ->relationshipName('roles', 'name')
                    ->label(__('Role'))
                    ->options(
                        fn () => Role::query()
                            ->where('guard_name', 'web')
                            ->whereNot('name', UserRole::SUPER_ADMIN)
                            ->whereNot('name', UserRole::SUPPORT)
                            ->pluck('name', 'id')
                            ->map(fn (string $name) => str(__($name))->ucfirst())
                            ->all(),
                    )
                    ->required()
                    ->validationAttribute(__('Role')),

                Forms\Components\Select::make('parkingLots')
                    ->multiple()
                    ->preload()
                    ->relationship('parkingLots', 'name')
                    ->exists('parking_lots', 'id')
                    ->hiddenOn([
                        Pages\EditUser::class,
                    ])
            ]);
    }
}
