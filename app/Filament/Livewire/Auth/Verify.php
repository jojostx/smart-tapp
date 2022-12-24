<?php

namespace App\Filament\Livewire\Auth;

use App\Actions\CreateTenantAdminUserAction;
use App\Events\Tenant\TenantVerified;
use App\Models\Tenant;
use App\Models\Tenant\User;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Livewire\Component;
use Stancl\Tenancy\Events\DatabaseCreated;
use Stancl\Tenancy\Exceptions\TenantDatabaseDoesNotExistException;

class Verify extends Component
{
    use WithRateLimiting;

    public string $email = '';

    public string $otp = '';

    public bool $emailSent = false;

    public bool $isCreatingAccount = false;

    public ?Tenant $tenant = null;

    public function mount(?string $id = null, bool $emailSent = false)
    {
        if (!blank($id)) {
            $this->tenant = Tenant::findOrFail($id);
            $this->email = $this->tenant->email;
        }

        $this->emailSent = \request('email_sent', $emailSent);
    }

    public function sendVerificationNotification()
    {
        // validate credentials
        $this->validateOnly('email', [
            'email' => ['required', 'string', 'email', 'exists:tenants,email'],
        ]);

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

        // retrieve tenant from database
        $this->tenant = Tenant::where(['email' => $this->email])->firstOrFail();

        // only proceed when we have an unverified tenant
        if ($this->tenantAlreadyPrepared) {
            $this->redirectToTenant();
        }

        // validate otp
        if (!$this->tenant->validateOTP($this->otp)->status) {
            $this->addError('otp', 'Invalid OTP');

            $this->isCreatingAccount = false;

            return;
        }

        if (!$this->tenant->markEmailAsVerified()) {
            $this->addError('otp', 'Unable to verify OTP');

            $this->isCreatingAccount = false;

            return;
        }

        // show tenant onboarding loading modal
        $this->isCreatingAccount = true;

        // @ this point we check if the database has been created already,
        if ($this->tenantDatabaseAlreadyExists()) {
            if ($this->tenantAdminUserExists()) {
                return $this->redirectIfSubdomainIsCreated();
            }

            // dispatch DatabaseCreated to trigger tenancy pipeline
            \event(new DatabaseCreated($this->tenant));

            return;
        }

        // only dispatch if tenant's db doesnt exists as the TenantVerified event
        // triggers a pipeline to create the tenant's db
        // and if the db already exists it will error and lock the tenant out.
        event(new TenantVerified($this->tenant));
    }

    public function redirectIfSubdomainIsCreated()
    {
        // retrieve tenant from database
        $this->tenant ??= Tenant::where(['email' => $this->email])->firstOrFail();

        // \dd($this->tenant, !$this->tenant->hasVerifiedEmail());

        if (blank($this->tenant) || !$this->tenant->hasVerifiedEmail()) {
            return;
        }

        // if tenant's domain has not been created
        if (blank($this->tenant->domains()->first())) {
            return;
        }

        if ($this->tenantDatabaseAlreadyExists()) {
            if ($this->tenantAdminUserExists()) {
                $this->redirectToTenant();
            }

            // if tenant super-admin does not exist, create one from the tenant model
            if (blank((new CreateTenantAdminUserAction)($this->tenant))) {
                return;
            }
        }
    }

    protected function redirectToTenant()
    {
        $subdomain = $this->tenant->domains()->first()->domain;

        $this->redirect(tenant_route($subdomain, 'filament.auth.login'));
    }

    protected function tenantAdminUserExists(): bool
    {
        try {
            return $this->tenant->run(function ($tenant) {
                return User::where('email', $tenant->email)->exists();
            });
        } catch (TenantDatabaseDoesNotExistException $e) {
            return false;
        }
    }

    protected function tenantDatabaseAlreadyExists(): bool
    {
        $manager = $this->tenant->database()->manager();

        return $manager->databaseExists($this->tenant->database()->getName());
    }

    public function getTenantHasDomainProperty()
    {
        return (bool) $this->tenant?->domains()->first()?->domain;
    }

    public function getTenantAlreadyPreparedProperty()
    {
        return $this->tenant &&
            $this->tenant->hasVerifiedEmail() &&
            $this->tenantDatabaseAlreadyExists() &&
            $this->tenantAdminUserExists() &&
            $this->tenantHasDomain;
    }

    public function render()
    {
        return view('livewire.auth.verify', ['title' => 'Verify your email address'])->extends('layouts.auth');
    }
}
