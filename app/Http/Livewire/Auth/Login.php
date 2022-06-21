<?php

namespace App\Http\Livewire\Auth;

use App\Models\Tenant\User;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    use WithRateLimiting;

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
            'domain' => ['required', 'exists:' . config('database.connections.mysql.driver') . '.domains,domain']
        ];
    }

    public function mount()
    {
        $this->fill([
            'domain' => $this->currentTenant?->domain ?? '',
        ]);
    }

    protected function prepareForValidation($attributes): array
    {
        if ($this->domain === $this->currentTenant?->domain) {
            return $attributes;
        }

        if (filled($this->domain) && \is_string($this->domain)) {
            $attributes['domain'] = strtolower($this->domain) . '.' . config('tenancy.central_domains')[0];
        }

        return $attributes;
    }

    public function authenticate()
    {
        // // set the user's session [user_id & tenant_id] on the central db session table before login
        // setTenantCentralSession(request());

        $this->rateLimit(20);

        $validated = $this->validate();

        /** @var \App\Models\Tenant $tenant */
        $tenant = $this->currentTenant ?? tenancy()->query()->where([
            'domain' => $validated['domain'],
        ])->first();

        $user = $tenant->run(function ($tenant) use ($validated) {
            return User::firstWhere('email', $validated['email']);
        });

        if (blank($user)) {
            $this->addError('email', __('validation.exists', 'Email'));

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

    public function getCurrentTenantProperty()
    {
        return \tenant();
    }

    public function render()
    {
        return view('livewire.auth.login')->extends('layouts.auth');
    }
}
