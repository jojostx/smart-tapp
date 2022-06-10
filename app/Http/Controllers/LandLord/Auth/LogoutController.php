<?php

namespace App\Http\Controllers\Landlord\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        Auth::guard('landlord')->logout();

        return redirect(route('home'));
    }
}