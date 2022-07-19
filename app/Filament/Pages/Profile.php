<?php

namespace App\Filament\Pages;

use App\Models\Tenant\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;

class Profile extends Page
{
    protected static string $view = 'filament.pages.profile';
    
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
            Section::make('General')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email Address')
                        ->required(),
                ]),
            Section::make('Update Password')
                ->columns(2)
                ->schema([
                    TextInput::make('current_password')
                        ->label('Current Password')
                        ->password()
                        ->rules(['required_with:new_password'])
                        ->currentPassword()
                        ->autocomplete('off')
                        ->columnSpan(1),
                    Grid::make()
                        ->schema([
                            TextInput::make('new_password')
                                ->label('New Password')
                                ->rules(['confirmed', Password::defaults()])
                                ->autocomplete('new-password'),
                            TextInput::make('new_password_confirmation')
                                ->label('Confirm Password')
                                ->password()
                                ->rules([
                                    'required_with:new_password',
                                ])
                                ->autocomplete('new-password'),
                        ]),
                ]),
        ];
    }

    public function submit(): void
    {
        \dd('ok');
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
