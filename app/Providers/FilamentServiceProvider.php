<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Facades\Filament;
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
      Filament::registerTheme(mix('css/filament.css'));
    });
  }
}
