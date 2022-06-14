<?php

namespace App\Http\Livewire\Auth;

use App\Events\UnverifiedTenantRegistered;
use App\Models\Tenant;
use App\Rules\Subdomain;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Register extends Component
{
    /** @var string */
    public $organization = '';

    /** @var string */
    public $domain = '';

    /** @var string fullyQualifiedSubdomain */
    public $fqsd = '';

    /** @var string */
    public $name = '';

    /** @var string */
    public $email = '';

    /** @var string */
    public $password = '';

    /** @var string */
    public $passwordConfirmation = '';

    /** @var string|int|bool */
    public $terms = '';

    protected $validationAttributes = [
        'organization' => 'Organization Name',
        'email' => 'email address',
        'fqsd' => 'Domain',
    ];

    protected function rules()
    {
        return [
            'organization' => ['required', 'string', 'max:255'],
            'domain' => ['bail', 'required', 'string', 'min:2', 'max:' . config('tenancy.subdomain_maxlength'), new Subdomain],
            'fqsd' => ['bail', 'required', 'string', 'min:2', 'unique:domains,domain', 'unique:tenants,domain'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'min:8', 'same:passwordConfirmation', Password::defaults()],
            'terms' => ['required', 'accepted'],
        ];
    }

    protected function prepareForValidation($attributes): array
    {
        $this->fqsd = $this->domain;
        
        if (filled($this->fqsd) && \is_string($this->fqsd)) {
            $attributes['fqsd'] = strtolower($this->fqsd) . '.' . config('tenancy.central_domains')[0];
        }
        
        return $attributes;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function register()
    {
        $result = $this->validate();

        // \dd($result);

        // create temporary unverified tenant
        $Tenant = Tenant::create([
            'name' => $result['name'],
            'email' => $result['email'],
            'password' => Hash::make($result['password']),
            'organization' => $result['organization'],
            'domain' => $result['fqsd'],
        ]);
        
        \dd($result, $Tenant);

        // redirect to email verification page
        return redirect()->intended(route('verification.notice', ['id' => $Tenant->getKey()]));
    }

    public function render()
    {
        return view('livewire.auth.register')->extends('layouts.auth');
    }
}
