<?php

use App\Filament\Livewire\Auth\AccountDeactivated;
use App\Filament\Livewire\Auth\PasswordRequest;
use App\Filament\Livewire\Auth\PasswordReset;
use App\Filament\Livewire\Auth\VerifyNewEmail;
use App\Http\Controllers\Subscription\CheckoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
*/

Route::name('filament.')
  ->group(function (): void {
      // filamentRedirectifauth {must not be logged in to access}
      Route::middleware(config('filament.middleware.base'))->group(function () {
          Route::get('password/reset/{token}', PasswordReset::class)
            ->name('auth.password.reset');

          Route::get('password/request', PasswordRequest::class)
            ->name('auth.password.request');

          Route::get('pending-email/verify/{token}', VerifyNewEmail::class)
            ->name('auth.pending-email.verify')
            ->middleware(['signed']);
      });

      Route::middleware(config('filament.middleware.auth'))->group(function () {
          Route::get('account-deactivated', AccountDeactivated::class)
            ->name('auth.account.deactivated');

          Route::prefix('plans')->as('plans.')->group(function () {
              Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
              Route::post('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
              Route::get('/checkout/update', [CheckoutController::class, 'update'])->name('checkout.update');
          });
      });
  });
