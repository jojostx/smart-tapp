<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilamentRedirectIfAuthenticated
{
    /**
     * The routes that should be redirected away from.
     *
     * @var array<int, string>
     */
    protected $protected_routes = [
        'filament.auth.login',
        'filament.auth.password.*',
        'auth.pending-email.verify',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if (request()->routeIs(...$this->protected_routes)) {
                    return redirect()->route('filament.pages.dashboard');
                }
            }
        }

        return $next($request);
    }
}
