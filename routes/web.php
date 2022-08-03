<?php

use App\Filament\Livewire\Auth\Login;
use App\Filament\Livewire\Auth\PasswordRequest;
use App\Filament\Livewire\Auth\Register;
use App\Filament\Livewire\Auth\Verify;
use Illuminate\Support\Facades\Route;

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

Route::view('/', 'welcome')->name('home');

Route::middleware(['web', 'guest'])
    ->withoutMiddleware('cookie_consent')
    ->group(function () {
        Route::get('register', Register::class)
            ->name('register');

        Route::get('login', Login::class)
            ->name('login');

        Route::get('email/verify/{id?}', Verify::class)
            ->name('verification.notice');

        Route::get('password/request', PasswordRequest::class)
            ->name('password.request');
    });
