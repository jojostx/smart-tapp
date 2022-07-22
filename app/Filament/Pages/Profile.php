<?php

namespace App\Filament\Pages;

use App\Models\Tenant\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;
use Filament\Pages\Actions;
use App\Filament\Forms\Components\Password as FilamentPasswordRevealPassword;
use App\Filament\Forms\Components\PhoneNumberInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;

class Profile extends Page
{
    protected static string $view = 'filament::pages.profile';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 2;

    /**
     * @var array<string, string>
     */
    public array $formData;

    public function mount(): void
    {
        $this->form->fill([
            'name'  =>  $this->getFormModel()->name,
            'email' =>  $this->getFormModel()->email,
            'phone_number' =>  $this->getFormModel()->phone_number,
        ]);
    }

    protected function getFormStatePath(): string
    {
        return 'formData';
    }

    protected function getFormModel(): Model | string | null
    {
        return User::find(auth()->guard('web')->id());
    }

    public function getCancelButtonUrlProperty(): string
    {
        return static::getUrl();
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Profile',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()->schema([
                Grid::make()
                    ->schema([
                        Section::make('General Information')
                            ->columns(1)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->required(),
                                PhoneNumberInput::make('phone_number')
                                    ->label('Phone')
                                    ->required()
                                    ->reactive()
                                    ->allowedCountries(['NG']),
                            ]),
                        Section::make('Password Information')
                            ->columns(1)
                            ->schema([
                                FilamentPasswordRevealPassword::make('current_password')
                                    ->label('Current Password')
                                    ->password()
                                    ->rules(['required_with:new_password'])
                                    ->currentPassword()
                                    ->placeholder('••••••••')
                                    ->autocomplete('off')
                                    ->columnSpan(1),
                                FilamentPasswordRevealPassword::make('new_password')
                                    ->label('New Password')
                                    ->generatable()
                                    ->passwordLength(12)
                                    ->passwordUsesNumbers()
                                    ->placeholder('••••••••')
                                    ->passwordUsesSymbols()
                                    ->rules(['confirmed', Password::defaults()])
                                    ->autocomplete('new-password'),
                                FilamentPasswordRevealPassword::make('new_password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->placeholder('••••••••')
                                    ->rules([
                                        'required_with:new_password',
                                    ])
                                    ->autocomplete('new-password'),
                            ]),
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),
                Card::make()
                    ->schema([
                        Placeholder::make('email_verified_at')
                            ->label('Email verified at')
                            ->content(fn (?User $record): string => $record ? ($record->email_verified_at?->diffForHumans() ?? '-') : '-'),
                        Placeholder::make('phone_verified_at')
                            ->label('Phone verified at')
                            ->content(fn (?User $record): string => $record ? ($record->phone_verified_at?->diffForHumans() ?? '-') : '-'),
                        Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (?User $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                        Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?User $record): string => $record ? $record->created_at->diffForHumans() : '-')
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ])->columns([
                'sm' => 4,
                'lg' => 4,
            ]),

        ];
    }

    public function submit(): void
    {
        \dd($this->validate());
        // $data = $this->form->getState();

        // $state = array_filter([
        //     'name'     => $data['name'],
        //     'email'    => $data['email'],
        //     'password' => $data['new_password'] ? Hash::make($data['new_password']) : null,
        // ]);

        // $this->getFormModel()->update($state);

        // if ($data['new_password']) {
        //     // @phpstan-ignore-next-line
        //     Filament::auth()->login($this->getFormModel(), (bool)$this->getFormModel()->getRememberToken());
        // }

        // $this->notify('success', strval(__('filament::resources/pages/edit-record.messages.saved')));
    }
}

// navigation items //
// billing
//  plans
//  subscriptions
//  payment methods
