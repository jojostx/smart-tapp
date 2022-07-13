<?php

namespace App\Filament\Livewire\Tenant\Components;

use Livewire\Component;

class QrcodeScanner extends Component
{
    public $parking_lot_id = '';
    public $vehicle_id = '';
    public $driver_id = '';

    public function mount(){}

    public function authenticate()
    {
        \dd('ok');
    }

    public function render()
    {
        return view('livewire.tenant.components.qrcode-scanner')->extends('layouts.auth');
    }
}
