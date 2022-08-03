<?php

declare(strict_types=1);

namespace App\Filament\Livewire\Auth;

use App\Filament\Forms\Components\Password as ComponentsPassword;
use App\Models\Tenant\User;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\PasswordReset as PasswordReset_;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Livewire\Component;

/**
 * @property ComponentContainer $form
 */
class PasswordReset extends Component implements HasForms
{
  use InteractsWithForms;
  use WithRateLimiting;

  public ?string $email = '';
  public ?string $token = '';
  public ?string $password = '';
  public ?string $password_confirm = '';

  public function mount(?string $token = ''): void
  {
    if (Filament::auth()->check()) {
      redirect()->intended(Filament::getUrl());
    }

    $this->email = request()->query('email', '');

    $this->token = $token;

    abort_if((blank($this->email) || blank($this->email)), 404);

    $this->form->fill();
  }

  protected function getFormSchema(): array
  {
    return [
      ComponentsPassword::make('password')
        ->label(__('Password'))
        ->password()
        ->hint('must be at least 10 characters')
        ->placeholder('••••••••')
        ->required()
        ->rules(['confirmed', RulesPassword::defaults()]),
      ComponentsPassword::make('password_confirmation')
        ->label('Confirm Password')
        ->password()
        ->placeholder('••••••••')
        ->rules([
          'required_with:new_password',
        ]),
    ];
  }

  public function submit()
  {
    $data = $this->form->getState();

    $credentials = [
      'email' => $this->email,
      'password' => $data['password'],
      'password_confirmation' => $data['password_confirmation'],
      'token' => $this->token,
    ];

    $response = Password::broker('users')->reset(
      $credentials,
      function (User $user, string $password): void {
        if (!$user->hasVerifiedEmail()) {
          $user->forceFill(['email_verified_at' => $this->freshTimestamp()]);
        }

        if ($user->isInactive()) {
          $user->activateAccount(false);
        }

        $user->forceFill([
          'password' => Hash::make($password),
          'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset_($user));
      },
    );

    if ($response === Password::PASSWORD_RESET) {
      Notification::make('success')
        ->title(__('passwords.reset'))
        ->success()
        ->send();

      return redirect(route('filament.auth.login', [
        'email' => $this->email,
      ]));
    }

    $this->addError('password', __($response));
  }

  public function render(): View
  {
    return view('livewire.auth.password-reset', ['title' => 'Create New Password'])->extends('layouts.auth');
  }
}
