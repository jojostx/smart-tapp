<?php

use App\Filament\Livewire\Auth\Login;
use App\Filament\Livewire\Auth\PasswordRequest;
use App\Filament\Livewire\Auth\Register;
use App\Filament\Livewire\Auth\Verify;
use App\Filament\Livewire\Auth\VerifyPendingTenant;
use App\Http\Controllers\HandleAfricasTalkingWebhookReport;
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

Route::view('/', 'pages.welcome')->name('home');
Route::view('/features', 'pages.features')->name('features');
Route::view('/pricing', 'pages.plans.index')->name('pricing');


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

Route::webhooks('termii-webhook-url', 'termii-webhook-url');
Route::post('africastalking-webhook-url', HandleAfricasTalkingWebhookReport::class);
