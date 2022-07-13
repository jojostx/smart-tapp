<?php

declare(strict_types=1);

use App\Filament\Livewire\Tenant\Components\QrcodeScanner;
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
    // /{tenant_id}/{parking_lot_id}
    Route::get('/', QrcodeScanner::class)->middleware('guest');

    Route::get('/impersonate/{token}', function ($token) {
        $redirectResponse = UserImpersonation::makeResponse($token);

        if ($redirectResponse->getTargetUrl()) {
            session(['impersonated' => true, 'targetUrl' => $redirectResponse->getTargetUrl()]);
        }

        return $redirectResponse;
    });
});
