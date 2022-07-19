<?php

namespace App\Filament\Resources;

use App\Enums\Roles\UserRole;
use App\Filament\Forms\Components\RoleSelect;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Tenant\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                Grid::make()
                    ->schema([
                        self::detailsSection(),
                    ])
                    ->columns(1)
                    ->columnSpan([
                        'sm' => 2,
                    ]),
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
                    // ->getStateUsing(function (User $record) {
                    //     return implode(',', $record->rolesArray);
                    // })->separator(','),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\BooleanColumn::make('email_verified_at')
                    ->label('Email verified')
                    ->default(false)
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date(config('filament.date_format'))
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
                Forms\Components\TextInput::make('Name')
                    ->label(__('Name'))
                    ->validationAttribute(__('Name'))
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->validationAttribute(__('Email'))
                    ->unique(table: User::class, ignorable: fn (?User $record): ?User => $record)
                    ->required()
                    ->email(),
                RoleSelect::make('role')
                    ->label(__('Role'))
                    ->validationAttribute(__('Role')),
            ]);
    }
}
