<?php

namespace App\Http\Controllers\Landlord\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        Auth::guard('landlord')->logout();

        return redirect(route('home'));
    }
}

