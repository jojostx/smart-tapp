<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Enums\Models\AccessStatus;
use App\Filament\Resources\AccessResource;
use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\ParkingLot;
use App\Models\Tenant\Vehicle;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccess extends CreateRecord
{
    protected static string $resource = AccessResource::class;
    public $vehicle;
    public $driver;
    public $parking_lot;

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament::resources/pages/create-record.form.actions.cancel.label'))
            ->action(function () {
                // cleanup dangling records here
                return redirect(static::getResource()::getUrl());
            })
            ->color('secondary');
    }

    public function beforeCreate()
    {
        $data = $this->form->getState();

        // find vehicle
        $this->vehicle = Vehicle::find(intval($data['vehicle_id']));


        if (blank($this->vehicle)) {
            return $this->addError('vehicle_id', 'Unable to create Access for invalid Vehicle');
        }

        // find driver
        $this->driver = Driver::find(intval($data['driver_id']));

        if (blank($this->driver)) {
            return $this->addError('driver_id', 'Unable to create Access for invalid Driver');
        }

        // find Parking Lot
        $this->parking_lot = ParkingLot::find(intval($data['parking_lot_id']));

        if (blank($this->parking_lot)) {
            return $this->addError('parking_lot_id', 'Unable to create Access for invalid Parking Lot');
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $access = new Access;

        // create access, attach driver and vehicle models and set it's status
        try {
            $access->driver()->associate($this->driver);
            $access->vehicle()->associate($this->vehicle);
            $access->parkingLot()->associate($this->parking_lot);
            $access->creator()->associate(auth()->user());
            $access->issuer()->associate(auth()->user());

            $access->fill([
                "expiry_period" => intval($data['expiry_period']),
                "validity_period" => intval($data['validity_period']),
            ]);

            $saved = match ($status = AccessStatus::from($data['status'])) {
                AccessStatus::ISSUED => $access->issue(),
                AccessStatus::ACTIVE => $access->activate(),
                AccessStatus::INACTIVE => $access->deactivate(),
                default => false,
            };

            if ($saved && \in_array($status, [AccessStatus::ACTIVE, AccessStatus::ISSUED])) {
                $access->sendAccessActivationNotification(checkStatusCountdown: 30);
            }

            \dd($access);

            return $access->refresh();
        } catch (\Throwable $th) {
            \dd($access);

            return $access;
        }
    }
}
