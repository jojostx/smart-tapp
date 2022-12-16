<?php

namespace App\Filament\Livewire\Auth;

use App\Filament\Traits\WithDomainValidation;
use App\Models\Tenant\User;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Hash;
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
            $this->addError('email', __('The email is Invalid'));

            return;
        }

        if (!Hash::check($validated['password'], $user->password)) {
            $this->addError('password', __('auth.password'));

            return;
        }

        $token = tenancy()->impersonate($tenant, $user->id, $this->redirectUrl);
        $domain = $tenant->domain;

        // $central_domain = '.' . \config('tenancy.central_domains.main');
        // $tenant_route = $domain . '/' . $this->redirectUrl;

        // Cookie::queue('impersonated', $tenant_route, 2628000, null, $central_domain);

        return redirect("https://$domain/impersonate/{$token->token}");
    }

    public function render()
    {
        return view('livewire.auth.login', ['title' => 'Sign in to your account'])
            ->extends('layouts.auth');
    }
}
