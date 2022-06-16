<?php

use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\Auth\Verify;
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

Route::middleware(['guest', 'landlord.guest'])->group(function () {
    Route::get('register', Register::class)
        ->name('register');

    Route::get('email/verify/{id?}', Verify::class)
        ->name('verification.notice');
});
