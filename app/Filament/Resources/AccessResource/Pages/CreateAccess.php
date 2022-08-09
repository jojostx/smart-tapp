<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Enums\Models\AccessStatus;
use App\Filament\Resources\AccessResource;
use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\ParkingLot;
use App\Models\Tenant\Vehicle;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccess extends CreateRecord
{
    protected static string $resource = AccessResource::class;

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

    protected function handleRecordCreation(array $data): Model
    {
        // find vehicle
        $vehicle = Vehicle::find(intval($data['vehicle_id']));

        if (blank($vehicle)) {
            return $this->addError('vehicle_id', 'Unable to create Access for invalid Vehicle');
        }

        // find driver
        $driver = Driver::find(intval($data['driver_id']));

        if (blank($driver)) {
            return $this->addError('driver_id', 'Unable to create Access for invalid Driver');
        }

        // find Parking Lot
        $parking_lot = ParkingLot::find(intval($data['parking_lot_id']));

        if (blank($parking_lot)) {
            return $this->addError('parking_lot_id', 'Unable to create Access for invalid Parking Lot');
        }

        /**
         * @var Access $access
         */
        $access = new (static::getModel());

        // create access, attach driver and vehicle models and set it's status
        try {
            $access->driver()->associate($driver);
            $access->vehicle()->associate($vehicle);
            $access->parkingLot()->associate($parking_lot);
            $access->creator()->associate(auth()->user());
            $access->issuer()->associate(auth()->user());
            
            $access->fill([
                "expiry_period" => intval($data['expiry_period']),
                "validity_period" => intval($data['validity_period']),
            ]);

            $return_value = match (AccessStatus::from($data['status'])) {
                AccessStatus::ISSUED => $access->issue(shouldNotify: true),
                AccessStatus::ACTIVE => $access->activate(shouldNotify: true),
                AccessStatus::INACTIVE => $access->deactivate(),
                default => false,
            };

            return $return_value && $access->refresh();
        } catch (\Throwable $th) {
            return $access;
        }
    }
}
