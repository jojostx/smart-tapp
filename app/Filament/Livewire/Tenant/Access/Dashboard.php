<?php

namespace App\Filament\Livewire\Tenant\Access;

use App\Models\Tenant\Access;
use App\Models\Tenant\ReparkRequest;
use App\Models\Tenant\User;
use App\Notifications\Tenant\User\ReparkRequestCreatedNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Forms;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;

class Dashboard extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use WithRateLimiting;

    public Access $access;
    public User $issuer;
    public string $plate_number = "";

    public function mount(Access $access)
    {
        $this->access = $access->load(['driver', 'issuer']);

        if (!auth('driver')->check() && $this->access->isActive()) {
            Auth::guard('driver')->login($this->access->driver);
        }

        $this->issuer = $this->access->issuer;

        $this->clearRateLimiter('submit');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('plate_number')
                ->required()
                ->exists('vehicles', 'plate_number'),
        ];
    }

    public function cancelRequest()
    {
        $this->reset('plate_number');
        $this->resetValidation();
    }

    public function getIsValidAccessProperty()
    {
        return $this->access->isValid();
    }

    public function getIsBlockingAnotherProperty()
    {
        return false;
    }

    protected function handleBlockersAccessRetrieval(string $blocker_plate_number = ""): ?Access
    {
        // find access by plate number and parking lot, if the access is not deactivated,
        return Access::query()
            ->whereNotInactive()
            ->whereRelation('vehicle', 'plate_number', $blocker_plate_number)
            ->latest()
            ->with(['driver', 'issuer'])
            ->first();
    }

    protected function handleCreateRecord(Access $blocker_access): bool
    {
        // create a repark request and notify the admins who issued the blocker's and the blockee's access
        // if the blocker and blockee are not in the same parking lot, assigned 'mismatch or inconsitent' to the request
        // after creation, send a [ReparkRequestCreatedNotification] notification to the issuer admins for both accesses
        $reparkRequest = ReparkRequest::createFromAccess($blocker_access, $this->access);

        if (blank($reparkRequest)) {
            return false;
        }

        $notifiables = $blocker_access->issuer->isNot($this->access->issuer) ? [
            $blocker_access->issuer,
            $this->access->issuer,
        ] : [
            $blocker_access->issuer,
        ];

        Notification::sendNow($notifiables, new ReparkRequestCreatedNotification($reparkRequest));

        return true;
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

        if ($this->plate_number === $this->access->vehicle->plate_number) {
            $this->addError("plate_number", "Cannot Request a Repark for your own Vehicle.");
            $this->clearRateLimiter();

            return;
        }

        $access = $this->handleBlockersAccessRetrieval($this->form->getState()['plate_number']);

        if (blank($access)) {
            $this->addError('plate_number', 'You are not allowed to request a repark for this vehicle.');

            $this->dispatchBrowserEvent('open-alert', [
                'color' => "danger",
                'message' => "You are not allowed to request a repark for this vehicle.",
                'timeout' => 10000
            ]);

            $this->clearRateLimiter();

            return;
        }

        if (!$this->handleCreateRecord($access)) {
            $this->dispatchBrowserEvent('close-modal', ['id' => 'request-repark']);

            $this->dispatchBrowserEvent('open-alert', [
                'color' => "danger",
                'message' => "Unable to request for a repark for this vehicle. Try again or Contact Support",
                'timeout' => 10000
            ]);

            $this->clearRateLimiter();

            return;
        }

        $this->dispatchBrowserEvent('close-modal', ['id' => 'request-repark']);

        $this->dispatchBrowserEvent('open-alert', [
            'color' => "success",
            'message' => "Your request has been submitted and is being processed by the admin",
            'timeout' => 10000
        ]);

        $this->cancelRequest();
    }

    public function render()
    {
        return view('livewire.tenant.access.dashboard')->extends('layouts.auth');
    }
}
