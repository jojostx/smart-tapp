<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Livewire\Auth\PasswordRequest;
use App\Filament\Livewire\Auth\PasswordReset;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// dd(config('filament.domain'), config('filament.middleware.base'), config('filament.path'));

Route::name('filament.')
  ->group(function (): void {
    Route::get('password/request', PasswordRequest::class)
      ->name('auth.password.request');

    Route::get('password/reset/{token?}', PasswordReset::class)
      ->name('auth.password.reset');
  });
