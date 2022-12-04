<?php

namespace App\Http\Responses\Tenant\Auth;

use Filament\Http\Responses\Auth\LogoutResponse as AuthLogoutResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;

class LogoutResponse extends AuthLogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        // $central_domain = '.' . \config('tenancy.central_domains.main');

        // Cookie::queue('impersonated', null, 2628000, null, $central_domain);

        return parent::toResponse($request);
    }
}
