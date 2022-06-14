<?php

namespace App\Http\Livewire\Auth;

use App\Models\Tenant;
use Livewire\Component;

class Verify extends Component
{
    /** @var string */
    public $email;

    public Tenant $verifiable;

    /** @var string */
    public $emailSentMessage;

    public function mount($id = null)
    {
        \dd($tenant = Tenant::find($id), isset($tenant), $id);

        // if ($verifiable instanceof Model && $verifiable instanceof MustVerifyEmail) {
        //     $this->verifiable = $verifiable;
        // } else if (is_string($verifiable)) {
        //     dd('is_string:', $verifiable);
        // } else {
        //     \dd('null route parameter',  $verifiable);
        // }
    }

    public function sendEmailVerificationMail()
    {
        // Auth::attempt([]);

        // if (Auth::user()->hasVerifiedEmail()) {
        //     redirect(route('home'));
        // }

        // Auth::user()->sendEmailVerificationNotification();

        // $this->emit('resent');

        // if ($response == Password::RESET_LINK_SENT) {
        //     $this->emailSentMessage = trans($response);

        //     return;
        // }

        // session()->flash('resent');
    }

    public function verifyEmail()
    {
        # code... 
        // verifiy email

        // if (Auth::user()->hasVerifiedEmail()) {
        //      Emit VerifiedEvent
        //      create tenant and domain
        //      $tenant->domains()->create(['domain' => $result['fqsd']]);
        //      after creation, redirect to admin login
        // }
    }

    public function render()
    {
        // return view('livewire.auth.verify')->extends('layouts.auth');
    }
}
