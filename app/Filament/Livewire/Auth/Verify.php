<?php

namespace App\Filament\Livewire\Auth;

use App\Events\Tenant\TenantVerified;
use App\Models\Tenant;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Livewire\Component;

class Verify extends Component
{
    use WithRateLimiting;

    public string $email = '';

    public string $otp = '';

    public Tenant $tenant;

    public bool $emailSent = false;

    public bool $isCreatingAccount = false;

    public function mount(?string $id = null)
    {
        if (! blank($id)) {
            $this->tenant = Tenant::findOrFail($id);

            $this->email = $this->tenant->email;

            $this->emailSent = true;
        } else {
            $this->emailSent = false;
        }
    }

    public function sendVerificationNotification()
    {
        // validate credentials
        $this->validateOnly('email', ['required', 'string', 'email', 'exists:tenants,email']);

        // retrieve tenant from database
        $this->tenant = Tenant::where(['email' => $this->email])->firstOrFail();

        // send verification notification
        $this->tenant->sendEmailVerificationNotification();

        // show otp verification section
        $this->emailSent = true;
    }

    public function verifyEmail()
    {
        // validate credentials
        $this->validate([
            'otp' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'exists:tenants,email'],
        ]);

        // only proceed when we have an unverified tenant
        if (! $this->tenant instanceof Tenant && $this->tenant->hasVerifiedEmail()) {
            return;
        }

        // validate otp
        if (! $this->tenant->validateOTP($this->otp)->status) {
            $this->addError('otp', 'Invalid OTP');

            return;
        }

        if ($this->tenant->markEmailAsVerified()) {
            //  show tenant onboarding loading modal
            $this->isCreatingAccount = true;

            //  Emit VerifiedEvent
            event(new TenantVerified($this->tenant));
        }
    }

    public function redirectIfSubdomainIsCreated()
    {
        if (blank($this->tenant)) {
            return;
        }

        if (blank($this->tenant->domains()->first())) {
            return;
        }

        $subdomain = $this->tenant->domains()->first()->domain;

        return redirect(tenant_route($subdomain, 'filament.auth.login'));
    }

    public function render()
    {
        return view('livewire.auth.verify', ['title' => 'Verify your email address'])->extends('layouts.auth');
    }
}
