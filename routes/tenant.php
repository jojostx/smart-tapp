<?php

declare(strict_types=1);

use App\Filament\Livewire\Tenant\Components\QrcodeScanner;
use App\Http\Middleware\InitializeTenancyByDomain;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// logout and redirect after access timeout

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function (Request $request) {
        dd($request);
    })->middleware('guest')->name('access.home');

    // /access/{access}
    Route::middleware(['access.valid'])->group(function ()
    {
        Route::get('/access/{access}/scan', QrcodeScanner::class)
            ->middleware('guest')
            ->name('access.scan');
    
        Route::get('/access/{access}/', function (Request $request) {
            dd($request);
        })->middleware('auth:driver')->name('access.dashboard');
    });

    Route::get('/impersonate/{token}', function ($token) {
        $redirectResponse = UserImpersonation::makeResponse($token);

        if ($redirectResponse->getTargetUrl()) {
            session(['impersonated' => true, 'targetUrl' => $redirectResponse->getTargetUrl()]);
        }

        return $redirectResponse;
    });
});
