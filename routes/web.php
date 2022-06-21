<?php

use App\Http\Livewire\Auth\Login;
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

// Route::get('/', function (Request $request)
// {
//     // $sessionModel = SessionModel::findOrFail($request->session()->getId());

//     // $sessionModel->tenant_id = tenant('id');

//     // $sessionModel->save();

//     dd(setTenantCentralSession($request, $request->user()?->id));
// })->name('home');

Route::middleware(['web', 'guest', 'landlord.guest'])->withoutMiddleware('cookie_consent')->group(function () {
    Route::get('register', Register::class)
        ->name('register');

    Route::get('login', Login::class)
        ->name('login');

    Route::get('email/verify/{id?}', Verify::class)
        ->name('verification.notice');
});
