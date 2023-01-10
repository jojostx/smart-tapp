<?php

namespace App\Filament\Livewire\Auth;

use App\Filament\Traits\WithDomainValidation;
use App\Models\PendingTenant;
use App\Models\Tenant;
use App\Models\Tenant\User;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class Login extends Component
{
    use WithRateLimiting;
    use WithDomainValidation;

    /** @var string */
    protected $redirectUrl = '/admin';

    /** @var string */
    public $email = '';

    /** @var string */
    public $domain = '';

    /** @var string */
    public $password = '';

    /** @var bool */
    public $remember = false;

    protected function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function mount()
    {
        $this->email = request()->query('email', '');

        $this->fill([
            'domain' => $this->currentTenant?->domain ?? '',
        ]);
    }

    public function authenticate()
    {
        $this->rateLimit(20);

        $validated = $this->validate();

        if (filled($redirectTo = $this->redirectIfUnverified($validated))) {
            return $redirectTo;
        }

        /** @var \App\Models\Tenant $tenant */
        $tenant = $this->currentTenant ??
            tenancy()->query()->where([
                'domain' => $validated['domain'],
            ])->first();

        $user = $tenant->run(function ($tenant) use ($validated) {
            return User::firstWhere('email', $validated['email']);
        });

        if (blank($user)) {
            $this->addError('email', __('The email is Invalid'));

            return;
        }

        if (!Hash::check($validated['password'], $user->password)) {
            $this->addError('password', __('auth.password'));

            return;
        }

        $token = tenancy()->impersonate($tenant, $user->id, $this->redirectUrl);
        $domain = $tenant->domain;

        return redirect("https://$domain/impersonate/{$token->token}");
    }

    protected function redirectIfUnverified(array $validated)
    {
        // check if a tenant with the email && domain exists
        if ($tenant = Tenant::whereUnverified($validated['email'], $validated['domain'])->first()) {
            // redirect to email verification page
            return redirect()->route('verification.notice', ['id' => $tenant->getKey(), 'email_sent' => false]);
        }

        return tenancy()->central(function () use ($validated) {
            // check if a pending tenant with the email && domain exists
            if ($tenant = PendingTenant::whereUnverified($validated['email'], $validated['domain'])->first()) {
                // redirect to pending tenant email verification page
                return redirect(central_route(
                    config('tenancy.central_domains.main'),
                    'verification.pending.notice',
                    ['id' => $tenant->getKey(), 'email_sent' => false]
                ));
            }

            return null;
        });
    }

    public function render()
    {
        return view('livewire.auth.login', ['title' => 'Sign in to your account'])
            ->extends('layouts.auth');
    }
}
