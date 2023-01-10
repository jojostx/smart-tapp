<?php

namespace App\Filament\Livewire\Auth;

use App\Actions\CreateTenantAdminUserAction;
use App\Events\Tenant\TenantVerified;
use App\Models\PendingTenant;
use App\Models\Tenant;
use App\Models\Tenant\User;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Livewire\Component;
use Stancl\Tenancy\Events\DatabaseCreated;
use Stancl\Tenancy\Exceptions\TenantDatabaseDoesNotExistException;

class VerifyPendingTenant extends Component
{
    use WithRateLimiting;

    public string $email = '';

    public string $otp = '';

    public bool $emailSent = false;

    public bool $isCreatingAccount = false;

    public ?PendingTenant $pendingTenant;

    public function mount(?int $id = null, bool $emailSent = false)
    {
        if (!blank($id)) {
            $this->pendingTenant = PendingTenant::findOrFail($id);
            $this->email = $this->pendingTenant->email;
        }

        $this->emailSent = \request('email_sent', $emailSent);
    }

    protected function getPendingTenant(): PendingTenant
    {
        $email = $this->pendingTenant?->email ?? $this->email;

        return PendingTenant::query()->where(['email' => $email])->firstOrFail();
    }

    protected function getTenant(): ?Tenant
    {
        $pendingTenant = $this->getPendingTenant();

        return Tenant::query()->where(['email' => $pendingTenant->email])->first();
    }

    /**
     * create a new tenant or return the existing tenant
     */
    protected function createTenantFromPending(): Tenant
    {
        $pendingTenant = $this->getPendingTenant();

        $tenant = $this->getTenant() ?? Tenant::query()->forceCreate([
            'name' => $pendingTenant->name,
            'email' => $pendingTenant->email,
            'password' => $pendingTenant->password,
            'domain' => $pendingTenant->domain,
            'organization' => $pendingTenant->organization,
            'email_verified_at' => $pendingTenant->email_verified_at,
        ]);

        return $tenant;
    }

    public function sendVerificationNotification()
    {
        // validate credentials
        $this->validateOnly('email', [
            'email' => ['required', 'string', 'email', 'exists:pending_tenants,email'],
        ]);

        // retrieve pending tenant from database
        $this->pendingTenant = $this->getPendingTenant();

        // send verification notification
        $this->pendingTenant->sendEmailVerificationNotification();

        // show otp verification section
        $this->emailSent = true;
    }

    public function redirectIfAccountHasBeenPrepared()
    {
        // retrieve tenant from database
        $tenant = $this->getTenant();

        if ($this->tenantAlreadyPrepared()) {
            $this->redirectToTenant();
        }

        if ($this->tenantDatabaseAlreadyExists() && !$this->tenantAdminUserExists()) {
            // if tenant super-admin does not exist, create one from the tenant model
            if (blank((new CreateTenantAdminUserAction)($tenant))) {
                return;
            }
        }
    }

    protected function redirectToTenant()
    {
        $tenant = $this->getTenant();

        if (\blank($tenant)) {
            return;
        }

        $subdomain = $tenant->domains()->first()->domain;

        $this->redirect(tenant_route($subdomain, 'filament.auth.login'));
    }

    protected function tenantAdminUserExists(): bool
    {
        $tenant = $this->getTenant();

        if (\blank($tenant)) {
            return false;
        }

        try {
            return $tenant->run(function ($tenant) {
                return User::where('email', $tenant->email)->exists();
            });
        } catch (TenantDatabaseDoesNotExistException $e) {
            return false;
        }
    }

    protected function tenantDatabaseAlreadyExists(): bool
    {
        $tenant = $this->getTenant();

        if (\blank($tenant)) {
            return false;
        }

        $manager = $tenant->database()->manager();

        return $manager->databaseExists($tenant->database()->getName());
    }

    protected function tenantHasDomain()
    {
        $tenant = $this->getTenant();

        return (bool) $tenant?->domains()->first()?->domain;
    }

    protected function tenantAlreadyPrepared()
    {
        $tenant = $this->getTenant();

        return $tenant?->hasVerifiedEmail() &&
            $this->tenantDatabaseAlreadyExists() &&
            $this->tenantAdminUserExists() &&
            $this->tenantHasDomain();
    }

    public function verifyEmail()
    {
        // validate credentials
        $this->validate([
            'otp' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'exists:pending_tenants,email'],
        ]);

        // retrieve pending tenant from database
        $this->pendingTenant = $this->getPendingTenant();

        // only pass when we have an unverified tenant
        if ($this->tenantAlreadyPrepared()) {
            $this->redirectToTenant();
        }

        // validate otp
        if (
            !$this->pendingTenant->validateOTP($this->otp)->status ||
            !$this->pendingTenant->markEmailAsVerified()
        ) {
            $this->addError('otp', 'Unable to verify Email');

            $this->isCreatingAccount = false;

            return;
        }

        // show tenant onboarding loading modal
        $this->isCreatingAccount = true;

        $tenant = $this->createTenantFromPending();

        if (!$this->tenantDatabaseAlreadyExists()) {
            // only dispatch if tenant's db doesnt exists as the TenantVerified event
            // triggers a pipeline to create the tenant's db
            // and if the db already exists it will error and lock the tenant out.
            event(new TenantVerified($tenant));
        }

        // @ this point we check if the database has been created already,
        $this->redirectIfAccountHasBeenPrepared();

        // dispatch DatabaseCreated to trigger tenancy pipeline
        \event(new DatabaseCreated($tenant));
    }

    public function render()
    {
        return view('livewire.auth.verify-pending-tenant', ['title' => 'Verify your email address'])
            ->extends('layouts.auth');
    }
}
