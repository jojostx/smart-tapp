<?php

use App\Filament\Livewire\Auth\Login;
use App\Filament\Livewire\Auth\PasswordRequest;
use App\Filament\Livewire\Auth\Register;
use App\Filament\Livewire\Auth\Verify;
use App\Filament\Livewire\Auth\VerifyPendingTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/**
 * GUESTS
 */
Route::view('/', 'pages.welcome')->name('home');
Route::view('/features', 'pages.features')->name('features');
Route::view('/pricing', 'pages.plans.index')->name('pricing');


/**
 * AUTHENTICATION
 */
Route::middleware(['web', 'guest'])
  ->withoutMiddleware('cookie_consent')
  ->group(function () {
    Route::get('login', Login::class)
      ->name('login');

    Route::get('register', Register::class)
      ->name('register')
      ->middleware('central'); // can only be accessed from central context

    Route::get('email/verify-pending/{id?}', VerifyPendingTenant::class)
      ->name('verification.pending.notice')
      ->middleware('central');

    Route::get('email/verify/{id?}', Verify::class)
      ->name('verification.notice');

    Route::get('password/request', PasswordRequest::class)
      ->name('password.request');
  });

/**
 * WEBHOOKS
 */
Route::prefix('webhooks')->group(function () {
  Route::webhooks('termii', 'termii');
  Route::webhooks('africastalking', 'africastalking');
  Route::post('mtn', function (Request $request)
  {
    logger($request);
    logger($request->all());

    return response("{'message': 'ok'}");
  });
});
