<?php

namespace App\Filament\Livewire\Tenant\Access;

use App\Models\Tenant\Access;
use Livewire\Component;

class Dashboard extends Component
{
    public Access $access;

    public function getIsValidAccessProperty()
    {
        return $this->access->isValid();
    }

    public function render()
    {
        return view('livewire.tenant.access.dashboard')->extends('layouts.auth');
    }
}
