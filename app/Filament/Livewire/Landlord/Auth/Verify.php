<?php

namespace App\Filament\Livewire\Landlord\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Verify extends Component
{
    public function resend()
    {
        if (Auth::guard('landlord')->user()->hasVerifiedEmail()) {
            redirect(route('home'));
        }

        Auth::guard('landlord')->user()->sendEmailVerificationNotification();

        $this->emit('resent');

        session()->flash('resent');
    }

    public function render()
    {
        return view('livewire.landlord.auth.verify')->extends('layouts.auth');
    }
}
