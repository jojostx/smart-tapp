<?php

namespace App\Filament\Livewire\Landlord\Auth\Passwords;

use Livewire\Component;

class Confirm extends Component
{
    /** @var string */
    public $password = '';

    public function confirm()
    {
        $this->validate([
            'password' => 'required|current_password',
        ]);

        session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('home'));
    }

    public function render()
    {
        return view('livewire.landlord.auth.passwords.confirm')->extends('layouts.auth');
    }
}
