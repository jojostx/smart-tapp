<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Enums\Models\AccessStatus;
use App\Enums\Models\FeatureResources;
use App\Filament\Resources\AccessResource;
use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\ParkingLot;
use App\Models\Tenant\Vehicle;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Jojostx\Larasubs\Models\Subscription;

class CreateAccess extends CreateRecord
{
    protected static string $resource = AccessResource::class;

    public $vehicle = null;

    public $driver = null;

    public $parking_lot = null;

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
        if (blank($this->vehicle = Vehicle::find(intval($data['vehicle_id'])))) {
            throw ValidationException::withMessages(['vehicle_id' => __('Unable to create Access for invalid Vehicle')]);
        }

        // find driver
        if (blank($this->driver = Driver::find(intval($data['driver_id'])))) {
            throw ValidationException::withMessages(['driver_id' => __('Unable to create Access for invalid Driver')]);
        }

        // find Parking Lot
        if (blank($this->parking_lot = ParkingLot::find(intval($data['parking_lot_id'])))) {
            throw ValidationException::withMessages(['parking_lot_id' => __('Unable to create Access for invalid Parking Lot')]);
        }

        if (!$this->canCreateAccessForParkingLot()) {
            Notification::make()
                ->body('You have reached the maximum access allocation for the selected parking lot and can not create any more accesses. **Consider upgrading your plan**')
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages(['parking_lot_id' => __('Unable to create Access for the Parking Lot')]);
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $access = new Access;

        // create access, attach driver and vehicle models and set it's status
        $access->fill([
            'expiry_period' => intval($data['expiry_period'] ?? 0),
            'validity_period' => intval($data['validity_period']),
            'issued_at' => \now(),
        ]);

        $access->driver()->associate($this->driver);
        $access->vehicle()->associate($this->vehicle);
        $access->parkingLot()->associate($this->parking_lot);
        $access->creator()->associate(auth()->user());
        $access->issuer()->associate(auth()->user());

        $access->save();

        $saved = match ($status = AccessStatus::from($data['status'])) {
            AccessStatus::ISSUED => $access->issue(),
            AccessStatus::ACTIVE => $access->activate(),
            AccessStatus::INACTIVE => $access->deactivate(),
            default => false,
        };

        if ($saved && \in_array($status, [AccessStatus::ACTIVE, AccessStatus::ISSUED])) {
            $access->sendAccessActivationNotification(checkStatusCountdown: 30);
        }

        return $access->refresh();
    }

    /**
     * check if parking lot accesses has reached limit for subscription
     *
     * @return bool
     */
    public function canCreateAccessForParkingLot()
    {
        if (\blank($this->parking_lot)) {
            return false;
        }

        $tenant = tenant();
        $used = $this->parking_lot->accesses->count();

        return tenancy()->central(function () use ($tenant, $used) {
            /** @var Subscription */
            $subscription = $tenant->subscription;
            $featureSlug = FeatureResources::PARKING_LOTS->value;

            if ($subscription->missingFeature($featureSlug)) {
                return false;
            }

            $max = $subscription->getMaxFeatureUnits($featureSlug);

            $feature = $subscription->plan->getFeatureBySlug($featureSlug);

            return ($used < $max) && $feature->isActive();
        });
    }
}
