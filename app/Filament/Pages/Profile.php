<?php

namespace App\Filament\Pages;

use Closure;
use App\Models\Tenant\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Forms\Components\Password as FilamentPasswordRevealPassword;
use Filament\Facades\Filament;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $this->personalInfoForm->fill([
            'name' => $this->getFormModel()->name,
            'email' => $this->getFormModel()->email,
            'phone_number' => $this->getFormModel()->phone_number,
        ]);

        $this->passwordInfoForm->fill([
            'new_password' => '',
            'new_password_confirmation' => '',
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

    protected function canUpdateInfo(): bool
    {
        return User::find(auth()->guard('web')->id())?->isSuperAdmin();
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

    protected function getPersonalInfoFormSchema(): array
    {
        return [
            Grid::make([
                'default' => 1
            ])
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->string()
                        ->maxLength(120)
                        ->required(),
                    TextInput::make('email')
                        ->label('Email')
                        ->helperText(
                            'To change your email you need to verify the new email address that you provide.
                            An email containing verification instructions will be sent to the provided email address.'
                        )
                        ->required()
                        ->maxLength(255)
                        ->unique('pending_user_emails', 'email')
                        ->unique('users', 'email', $this->getFormModel())
                        ->email(),
                    TextInput::make('phone_number')
                        ->label('Phone Number')
                        ->required()
                        ->reactive()
                        ->unique('users', 'phone_number', ignoreRecord: true)
                        ->tel()
                        ->rule(Rule::phone()->country(['NG'])),
                ])
                ->disabled(!self::canUpdateInfo()),
        ];
    }

    protected function getPlaceholderInfoFormSchema(): array
    {
        return [
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
        ];
    }

    protected function getPasswordInfoFormSchema(): array
    {
        return [
            Grid::make([
                'default' => 1,
            ])
                ->schema([
                    FilamentPasswordRevealPassword::make('current_password')
                        ->label('Current Password')
                        ->placeholder('••••••••')
                        ->rules(['required_with:new_password'])
                        ->currentPassword()
                        ->autocomplete('off'),
                    FilamentPasswordRevealPassword::make('new_password')
                        ->label('New Password')
                        ->placeholder('••••••••')
                        ->required()
                        ->different('current_password')
                        ->generatable()
                        ->maxLength(100)
                        ->passwordLength(10)
                        ->passwordUsesNumbers()
                        ->passwordUsesSymbols()
                        ->confirmed()
                        ->autocomplete('new-password'),
                    FilamentPasswordRevealPassword::make('new_password_confirmation')
                        ->label('Confirm Password')
                        ->placeholder('••••••••')
                        ->passwordLength(10)
                        ->requiredWith('new_password')
                        ->autocomplete('new-password'),
                ]),
        ];
    }

    protected function getForms(): array
    {
        $authUser = $this->getFormModel();

        return [
            'personalInfoForm' => $this->makeForm()
                ->schema($this->getPersonalInfoFormSchema())
                ->model($authUser),

            'placeholderInfoForm' => $this->makeForm()
                ->schema($this->getPlaceholderInfoFormSchema())
                ->model($authUser),

            'passwordInfoForm' => $this->makeForm()
                ->schema($this->getPasswordInfoFormSchema())
                ->model($authUser),
        ];
    }

    public function savePersonalInfo(): void
    {
        \abort_unless($this->canUpdateInfo(), 403);

        $data = $this->personalInfoForm->getState();
        $email_changed = isset($data['email']) && $data['email'] != $this->getFormModel()->email;

        // save phone and name attribute
        $result = DB::transaction(function () use ($data) {
            $saved = $this->getFormModel()->fill([
                'name' => $data['name'],
                'phone_number' => $data['phone_number'],
            ])->save();

            if ($saved && $tenant = tenant()) {
                $saved = $tenant->fill([
                    'name' => $data['name']
                ])->save();
            }

            // Create a pending user email
            $email_changed = (bool) $this->getFormModel()->newEmail($data['email']);

            return ['saved' => $saved, 'email_changed' => $email_changed];
        });

        $success_message = 'Details saved successfully. ';

        if ($email_changed && $result['email_changed']) {
            $success_message .= 'Please check your inbox to verify the new email.';
        }

        $result['saved'] && $this->showSuccessNotification($success_message);
    }

    public function savePasswordInfo(): void
    {
        if ($tenant = \tenant()) {
            /** @var \Illuminate\Contracts\Auth\Authenticatable|Model */
            $authUser = $this->getFormModel();
            $new_password = Hash::make($this->passwordInfoForm->getState()['new_password']);

            $saved = DB::transaction(function () use ($authUser, $tenant, $new_password) {
                return $authUser->forceFill([
                    'password' => $new_password,
                    'remember_token' => str()->random(60),
                ])->save()
                    &&
                    $tenant->forceFill([
                        'password' => $new_password,
                        'remember_token' => str()->random(60),
                    ])->save();
            });

            if ($saved) {
                Auth::guard('web')->logout();
                session()->invalidate();
                session()->regenerateToken();

                Filament::auth()->login($authUser, (bool) $authUser?->getRememberToken());

                event(new PasswordReset($authUser));

                $this->showSuccessNotification('Password updated successfully.');
            }
        }
    }

    protected function showSuccessNotification(string|Closure $body)
    {
        Notification::make('save-success-' . str()->random(5))
            ->body($body)
            ->success()
            ->seconds(10)
            ->send();
    }

    protected function showFailureNotification(string|Closure $body)
    {
        Notification::make('save-failed-' . str()->random(5))
            ->body($body)
            ->danger()
            ->seconds(10)
            ->send();
    }
}

// navigation items //
// billing
//  plans
//  subscriptions
//  payment methods
