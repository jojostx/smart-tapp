<?php

namespace App\Filament\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class AccountDeactivated extends Component
{
    public function render(): View
    {
        return view('livewire.auth.account-deactivated', ['title' => 'Account Deactivated'])
          ->extends('layouts.auth');
    }
}
