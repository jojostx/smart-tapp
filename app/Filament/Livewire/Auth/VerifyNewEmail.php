<?php

namespace App\Filament\Livewire\Auth;

use App\Models\Tenant\PendingUserEmail;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class VerifyNewEmail extends Component
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
        if ($tenant = \tenant()) {
            $user = DB::transaction(function () use ($tenant) {
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

                return $user;
            });

            if ($user) {
                event(new Verified($user));

                $this->authenticated($tenant);
            }
        }
    }

    public function authenticated($tenant)
    {
        $subdomain = $tenant->domains()->first()->domain;

        return redirect(tenant_route($subdomain, 'filament.auth.login'))->with('verified', true);
    }
}
