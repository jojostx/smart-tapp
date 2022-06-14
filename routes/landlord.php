<?php

use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\Landlord\Auth\LogoutController;
use App\Http\Livewire\Landlord\Auth\Login;
use App\Http\Livewire\Landlord\Auth\Passwords\Confirm;
use App\Http\Livewire\Landlord\Auth\Passwords\Email;
use App\Http\Livewire\Landlord\Auth\Passwords\Reset;
use App\Http\Livewire\Landlord\Auth\Verify;
use App\Http\Livewire\Landlord\Dashboard;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::redirect('/', '/login')->name('home');

// Login
Route::middleware('landlord.guest')->group(function () {
    Route::get('login', Login::class)
        ->name('login');

    Route::get('password/reset', Email::class)
        ->name('password.request');

    Route::get('password/reset/{token}', Reset::class)
        ->name('password.reset');
});


Route::middleware('landlord.auth')->group(function () {
    Route::get('email/verify', Verify::class)
        ->middleware('throttle:6,1')
        ->name('verification.notice');

    Route::get('email/verify/{id}/{hash}', EmailVerificationController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('dashboard', Dashboard::class)
        ->name('dashboard');

    Route::get('password/confirm', Confirm::class)
        ->name('password.confirm');

    Route::post('logout', LogoutController::class)
        ->name('logout');
});
