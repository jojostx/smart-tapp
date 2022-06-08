<?php

namespace App\Http\Controllers\Landlord\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Subdomain;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\Password;

class RegisteredTenantController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $result = $this->validate($request, [
            'organization' => 'required|string|max:255',
            'domain' => ['required', 'string', 'max:144', 'unique:domains', new Subdomain],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => 'required'
        ]);

        \dd('store', $result);
    }
}
