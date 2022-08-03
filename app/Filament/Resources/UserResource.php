<?php

namespace App\Filament\Resources;

use App\Enums\Models\UserAccountStatus;
use Filament\Forms;
use Filament\Tables;
use App\Models\Tenant\User;
use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Enums\Roles\UserRole;
use Filament\Resources\Resource;
use App\Models\Tenant\ParkingLot;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Forms\Components\HelpCard;
use App\Filament\Forms\Components\PhoneNumberInput;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Forms\Components\SingleOptionMultiSelect;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

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
                                    <span>• You can only assign one available Parking lot per admin user.</span>
                                </li>
                                <li>
                                    <span>• Assigning a Parking lot to an Admin User makes it unavailable.</span>
                                </li>
                                <li>
                                    <span>• After creating an Admin User an email containing instructions to complete the account creation will be sent to their email address.</span>
                                </li>
                                <li>
                                    <span>• The Admin user will have access to the dashboard for administering the Parking lot assigned to them.</span>
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
                    ]),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\BooleanColumn::make('email_verified_at')
                    ->label('Email verified')
                    ->default(false)
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('phone_verified_at')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (User $record) => $record->isActive() ? 'Deactivate' : 'Activate')
                    ->button()
                    ->action(fn (User $record) => $record->isActive() ? $record->deactivateAccount() : $record->activateAccount())
                    ->color(fn (User $record) => $record->isActive() ? 'danger' : 'primary'),
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
            //
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
                    ->unique(table: User::class, ignorable: fn (?User $record): ?User => $record)
                    ->required()
                    ->email(),
                PhoneNumberInput::make('phone_number')
                    ->label('Phone')
                    ->required()
                    ->reactive()
                    ->allowedCountries(['NG']),
                SingleOptionMultiSelect::make('role')
                    ->relationshipName('roles', 'name')
                    ->label(__('Role'))
                    ->options(
                        fn () => Role::query()
                            ->where('guard_name', 'web')
                            ->whereNot('name', UserRole::SUPER_ADMIN)
                            ->pluck('name', 'id')
                            ->map(fn (string $name) => str(__($name))->ucfirst())
                            ->all(),
                    )
                    ->required()
                    ->validationAttribute(__('Role')),
                SingleOptionMultiSelect::make('parking_lot')
                    ->relationshipName('parkingLots', 'name')
                    ->label(__('Parking Lot'))
                    ->required()
                    ->options(
                        fn () => ParkingLot::query()
                            ->pluck('name', 'id')
                            ->map(fn (string $name) => str(__($name))->ucfirst())
                            ->all(),
                    )
                    ->validationAttribute(__('Parking Lot'))
                    ->helperText('This is the Parking lot that will be administered by the admin'),
            ]);
    }
}
