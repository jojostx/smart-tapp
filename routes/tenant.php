<?php

declare(strict_types=1);

use App\Filament\Livewire\Tenant\Access\Dashboard;
use App\Filament\Livewire\Tenant\Access\QrcodeScanner;
use App\Http\Middleware\InitializeTenancyByDomain;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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
        dd('ok');
    })->name('access.home');


    Route::prefix('access')->name('access.')->group(function () {
        Route::get('/{access}/scan', QrcodeScanner::class)
            ->name('scan');

        Route::get('/{access}/dashboard', Dashboard::class)
            ->middleware('auth:driver', 'access.valid')
            ->name('dashboard');

        Route::get('/{key?}', function (?string $key = null) {
            abort_if(blank($key), 404);

            $id = str($key)->substr(0, 1);
            $uuid_first_segment = str($key)->substr(1);

            $access = \App\Models\Tenant\Access::where('id', $id)->where('uuid', 'LIKE', "{$uuid_first_segment}-%")->first();

            abort_if(blank($access), 404);

            return redirect()->route('access.scan', compact('access'));
        })->name('redirect');
    });

    Route::get('/impersonate/{token}', function ($token) {
        $redirectResponse = UserImpersonation::makeResponse($token);

        if ($redirectResponse->getTargetUrl()) {
            session(['impersonated' => true, 'targetUrl' => $redirectResponse->getTargetUrl()]);
        }

        return $redirectResponse;
    });
});
