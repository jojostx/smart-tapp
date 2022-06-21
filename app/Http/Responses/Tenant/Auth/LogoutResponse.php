<?php

namespace App\Http\Responses\Tenant\Auth;

use Filament\Http\Responses\Auth\LogoutResponse as AuthLogoutResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;

class LogoutResponse extends AuthLogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        Cookie::queue(Cookie::forget(config('tenancy.cookie')));

        return parent::toResponse($request);
    }
}
