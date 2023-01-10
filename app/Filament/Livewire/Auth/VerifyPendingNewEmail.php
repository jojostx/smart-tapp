<?php

namespace App\Filament\Livewire\Auth;

use App\Jobs\Tenant\UpdateCustomerEmailOnFlutterwave;
use App\Models\Tenant\PendingUserEmail;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;

class VerifyPendingNewEmail extends Component
{
    use WithRateLimiting;

    public string $token;

    public function mount(string $token)
    {
        $this->token = $token;

        $this->verifyEmail();
    }

    public function verifyEmail()
    {
        $tenant = tenant();

        throw_if(blank($tenant), TenantCouldNotBeIdentifiedException::class);

        try {
            DB::beginTransaction();

            // validate credentials
            $user = PendingUserEmail::whereToken($this->token)
                ->firstOr(
                    ['*'],
                    function () {
                        throw new AuthenticationException(
                            __('The verification link is not valid anymore.')
                        );
                    }
                )->tap(function ($pendingUserEmail) {
                    $pendingUserEmail->activate();
                })->user;

            //change tenant email on the central db
            if (filled($user)) {
                $tenant->forceFill([
                    'email' => $user->email,
                    'email_verified_at' => now(),
                ])->save();
            }

            DB::commit();

            if ($user) {
                // dispatch a job to the queue to update the customer's email on flutterwave
                UpdateCustomerEmailOnFlutterwave::dispatch($user, $tenant)->afterCommit();

                event(new Verified($user));

                $this->authenticated($tenant);
            }
        } catch (\Exception $exp) {
            DB::rollBack();
        }
    }

    public function authenticated($tenant)
    {
        $subdomain = $tenant->domains()->first()->domain;

        return redirect(tenant_route($subdomain, 'filament.auth.login'))->with('verified', true);
    }
}
