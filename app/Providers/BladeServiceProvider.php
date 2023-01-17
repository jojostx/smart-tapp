<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as View_;
use Jojostx\Larasubs\Models\Plan;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('authtenant', function ($value = false) {
            return authenticatedTenant();
        });

        // Using closure based composers...
        View::composer(['pages.welcome', 'subscriptions.plans.index'], function (View_ $view) {
            $plans = Plan::whereActive()->get();

            $view->with('plans', $plans);
        });
    }
}
