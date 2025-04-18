<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EnsureAccountIsNotDeactivated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var \App\Models\Tenant\User */
        $user = $request->user('web');

        if ($user && $user->isAdmin() && $user->isDeactivated()) {
            auth('web')->logout();

            return Redirect::guest(route('filament.auth.account.deactivated'));
        }

        return $next($request);
    }
}
