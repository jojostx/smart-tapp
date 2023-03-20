<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register()
    {
    //
    }

    public function boot()
    {
        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Parking',
            ]);
        });

        Filament::serving(function () {
            Filament::registerTheme(mix('css/filament.css'));
            Filament::registerScripts([asset('js/phoneinput.js')]);
            Filament::registerScripts([asset('js/actionable-text-column.js')]);
            Filament::registerScripts([asset('js/filament-turbo.js')]);
            Filament::registerScripts([asset('js/filament-stimulus.js')]);
            Filament::registerScripts([asset('js/reload-listener.js')]);
        });

        Filament::registerRenderHook(
            'global-search.start',
            fn (): string => Blade::render('@livewire(\'components.subscription-indicator\')'),
        );
    }
}
