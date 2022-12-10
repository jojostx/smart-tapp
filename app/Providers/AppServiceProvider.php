<?php

namespace App\Providers;

use App\Http\Responses\Tenant\Auth\LogoutResponse as AuthLogoutResponse;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyBootstrapped;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('flattenWithKeys', function ($nestingDelimiter = '.', $prefix = '') {
            return collect(\flattenWithKeys($this->toArray(), $nestingDelimiter, $prefix));
        });
        
        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            \Spatie\Permission\PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->id;
        });

        $this->app->bind(LogoutResponse::class, AuthLogoutResponse::class);
    }
}
