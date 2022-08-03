<?php

declare(strict_types=1);

namespace App\Filament\Livewire\Auth;

use App\Filament\Traits\WithDomainValidation;
use App\Models\Tenant;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class PasswordRequest extends Component
{
  use WithRateLimiting;
  use WithDomainValidation;

  public ?Tenant $tenant = null;

  /** @var string */
  public $domain = '';

  public ?string $email = '';

  public bool $emailSent = false;

  protected array $rules = [
    'email' => ['required', 'email'],
  ];

  public function mount()
  {
    if (Filament::auth()->check()) {
      return redirect()->intended(Filament::getUrl());
    }

    $this->fill([
      'domain' => $this->currentTenant?->domain ?? '',
    ]);
  }

  public function send(): void
  {
    $data = $this->validate();

    if (blank($this->currentTenant)) {
      $tenant = tenancy()->query()->firstWhere("domain", $data['domain']);

      tenancy()->initialize($tenant);
    }

    $response = Password::broker('users')->sendResetLink([
      'email' => $data['email'],
    ]);

    if ($response === Password::RESET_LINK_SENT) {
      Notification::make('success')
        ->title(__('passwords.sent'))
        ->success()
        ->send();

      $this->emailSent = true;
    } else {
      $this->addError('email', __($response));
    }
  }

  public function render(): View
  {
    return view('livewire.auth.password-request', ['title' => 'Reset Password'])->extends('layouts.auth');
  }
}
