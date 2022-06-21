<?php

namespace App\Providers;

use App\Http\Responses\Tenant\Auth\LogoutResponse as AuthLogoutResponse;
use Filament\Http\Responses\Auth\LogoutResponse;
use Illuminate\Support\ServiceProvider;

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
        \app()->bind(
            LogoutResponse::class,
            AuthLogoutResponse::class
        );
    }
}
