<?php

namespace App\Filament\Livewire\Tenant\Components;

use Auth;
use App\Models\Tenant\Access;
use Livewire\Component;

class QrcodeScanner extends Component
{
    public Access $access;
    public $parking_lot;

    /**
     * [V] 0. verify access's driver phone_number
     * [V] 1. retrieve access using route model binding or throw 404 error.
     *      1.1. if status is expired, show expiry screen and prompt driver to contact [issuer's|support phone_number|email] for re-issue.
     * [V] 2. retrieve parking after scan lot id [encoded in qrcode].
     * [V] 3. check equality of the access's parking lot with that of the qrcode.
     *      3.1. dispatch browser event to notify driver if there is a mismatch.
     * [V] 4. set access's status to active.
     * [V] 5. login driver for access validity period.
     * 6. throttle to 5/hr callout if blocking occurs.
     */
    public function mount(Access $access)
    {
        $this->access = $access;
        if (! $this->access->driver->hasVerifiedPhoneNumber()) {
            $this->access->driver->markPhoneNumberAsVerified();
        }
    }

    public function getIsValidAccessProperty()
    {
        return $this->access->isValid();
    }

    public function updatedParkingLot()
    {
        $this->resetValidation('parking_lot');
        $this->authenticate();
    }

    public function authenticate()
    {
        if ($this->access->parkingLot()->whereUuid($this->parking_lot)->doesntExist()) {
            $this->addError('parking_lot', 'You are at the Wrong Parking lot');

            $this->dispatchBrowserEvent('open-alert', [
                'color' => "danger",
                'message' => 'The Parking Lot does not match the one assigned to you.',
                'timeout' => 30000
            ]);

            return;
        }

        $this->access->activate();

        Auth::guard('driver')->login($this->access->driver);

        return redirect()->route('access.dashboard');
    }

    public function render()
    {
        return view('livewire.tenant.components.qrcode-scanner')->extends('layouts.auth');
    }
}
