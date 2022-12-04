<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->mapWebRoutes();
        $this->mapApiRoutes();

        $this->addUrlGeneratorMacros();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    protected function mapWebRoutes()
    {
        foreach ($this->centralDomains() as $key => $domain) {
            if ($domain == env('TENANCY_CENTRAL_ADMIN_DOMAIN')) {
                Route::middleware('web')
                    ->as('landlord.')
                    ->domain($domain)
                    ->namespace($this->namespace)
                    ->group(base_path('routes/landlord.php'));
            } else {
                Route::middleware(['web'])
                    ->domain($domain)
                    ->namespace($this->namespace)
                    ->group(base_path('routes/web.php'));

                Route::domain(config('filament.domain'))
                    ->middleware(config('filament.middleware.base'))
                    ->prefix(config('filament.path'))
                    ->group(base_path('routes/filament-auth.php'));
            }
        }
    }

    protected function mapApiRoutes()
    {
        foreach ($this->centralDomains() as $key => $domain) {
            Route::prefix('api')
                ->domain($domain)
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        }
    }

    protected function centralDomains(): array
    {
        return config('tenancy.central_domains');
    }

    public function addUrlGeneratorMacros()
    {
        UrlGenerator::macro('tenantSignedRoute', function ($name, $parameters = [], $expiration = null, $absolute = true) {
            $this->ensureSignedRouteParametersAreNotReserved(
                $parameters = Arr::wrap($parameters)
            );

            if ($expiration) {
                $parameters = $parameters + ['expires' => $this->availableAt($expiration)];
            }

            ksort($parameters);

            $key = call_user_func($this->keyResolver);

            return tenant_route(tenant()->domains->first()->domain, $name, $parameters + [
                'signature' => hash_hmac('sha256', tenant_route(tenant()->domains->first()->domain, $name, $parameters, $absolute), $key),
            ], $absolute);
        });

        UrlGenerator::macro('temporaryTenantSignedRoute', function ($name, $expiration = null, $parameters = [],  $absolute = true) {
            return $this->tenantSignedRoute($name, $parameters, $expiration, $absolute);
        });
    }
}
