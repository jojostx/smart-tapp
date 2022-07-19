<?php

namespace App\Providers;

use App\Http\Responses\Tenant\Auth\LogoutResponse as AuthLogoutResponse;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\LogoutResponse;
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
        app()->bind(
            LogoutResponse::class,
            AuthLogoutResponse::class
        );

        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            \Spatie\Permission\PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->id;
        });

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Parking',
                // 'User Management',
                // 'Settings',
            ]);
        });
    }
}
