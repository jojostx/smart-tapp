<?php

namespace App\Filament\Livewire\Tenant\Access;

use App\Models\Tenant\Access;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Dashboard extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use WithRateLimiting;

    public Access $access;
    public string $plate_number = "";

    public function mount(Access $access)
    {
        $this->access = $access;

        if (!auth('driver')->check() && $this->access->isActive()) {
            Auth::guard('driver')->login($this->access->driver);
        }
    }

    protected function getFormSchema(): array 
    {
        return [
            Forms\Components\TextInput::make('plate_number')
                ->required(),
        ];
    }

    public function cancelRequest()
    {
        $this->reset('plate_number');
    }
 
    public function submit(): void
    {
        try {
            $this->rateLimit(1, 30 * 60);
        } catch (TooManyRequestsException $exception) {
            $this->dispatchBrowserEvent('open-alert', [
                'color' => "danger",
                'message' => "You've recently submitted a repark request. You will be allowed to request repark after {$exception->minutesUntilAvailable} minutes",
                'timeout' => 20000
            ]);

            return;
        }

        $data = $this->form->getState();

        // find access by plate number and parking lot, if the access is not deactivated,
        // create a repark request and notify the admins who issued the blocker's and the blockee's access
        // if the blocker and blockee are not in the same parking lot, assigned 'mismatch or inconsitent' to the request
        //  
        
        $this->dispatchBrowserEvent('close-modal', ['id' => 'request-repark']);
        
        $this->dispatchBrowserEvent('open-alert', [
            'color' => "success",
            'message' => "Your request has been submitted and is being processed by the admin",
            'timeout' => 10000
        ]);
    }

    public function getIsValidAccessProperty()
    {
        return $this->access->isValid();
    }

    public function getIsBlockingAnotherProperty()
    {
        return false;
    }

    public function render()
    {
        return view('livewire.tenant.access.dashboard')->extends('layouts.auth');
    }
}
