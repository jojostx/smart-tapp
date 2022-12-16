<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EnsureSubscriptionIsActive
{
    /**
     * The routes to allow access to when subscription is inactive
     */
    protected $except = [
        'filament.pages.settings',
        'filament.plans.*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var \App\Models\Tenant */
        $tenant = tenant();
        $subscription = $tenant?->subscription;

        if ((blank($subscription) || $subscription->isInactive()) && ! $request->routeIs(...$this->except)) {
            return Redirect::route('filament.pages.settings');
        }

        return $next($request);
    }
}
