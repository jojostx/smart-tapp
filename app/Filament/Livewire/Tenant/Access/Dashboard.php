<?php

namespace App\Filament\Livewire\Tenant\Access;

use App\Models\Tenant\Access;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public Access $access;

    public function mount(Access $access)
    {
        $this->access = $access;

        if (!auth('driver')->check() && $this->access->isActive()) {
            Auth::guard('driver')->login($this->access->driver);
        }
    }

    public function getIsValidAccessProperty()
    {
        return $this->access->isValid();
    }

    public function render()
    {
        return view('livewire.tenant.access.dashboard')->extends('layouts.auth');
    }
}
