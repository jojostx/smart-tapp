<?php

declare(strict_types=1);

use App\Http\Middleware\InitializeTenancyByDomain as MiddlewareInitializeTenancyByDomain;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
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

Route::middleware([
    'web',
    MiddlewareInitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        dd(\App\Models\Tenant\User::all());
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    })->middleware('guest');

    Route::get('/impersonate/{token}', function ($token) {
        $redirectResponse = UserImpersonation::makeResponse($token);

        if ($redirectResponse->getTargetUrl()) {
            session(['impersonated' => true, 'targetUrl' => $redirectResponse->getTargetUrl()]);
        }

        return $redirectResponse;
    });

    // Route::post('/filament/logout', function ($token) {
    //     dd('ok');

    //     return '';
    // })->name('filament.auth.logout');
});
