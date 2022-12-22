<?php

namespace App\Filament\Resources\ParkingLotResource\Pages;

use App\Enums\Models\FeatureResources;
use App\Filament\Resources\ParkingLotResource;
use App\Models\Tenant\ParkingLot;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Jojostx\Larasubs\Models\Subscription;

class CreateParkingLot extends CreateRecord
{
    protected static string $resource = ParkingLotResource::class;

    public function afterCreate(): void
    {
        Notification::make()
            ->body("Congratulations 🎉 You have successfully created a new Parking Lot.")
            ->success()
            ->persistent()
            ->send();
    }

    public function beforeCreate()
    {
        if (!$this->canCreateParkingLot()) {
            Notification::make()
                ->title('Unable to create Parking Lot')
                ->body('You have reached the maximum parking lot allocation for your current subscription and can not create any more parking lots. **Consider upgrading your plan**')
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages(['parking_lot_id' => __('Unable to create Parking Lot')]);
        }
    }

    /**
     * check if parking lot count has reached limit for subscription
     *
     * @return bool
     */
    public function canCreateParkingLot()
    {
        $tenant = \tenant();
        $used = ParkingLot::count();

        return \tenancy()->central(function () use ($tenant, $used) {
            /** @var Subscription */
            $subscription = $tenant->subscription;
            $featureSlug = FeatureResources::PARKING_LOTS->value;

            if ($subscription->missingFeature($featureSlug)) {
                return false;
            }

            $max = $subscription->getMaxFeatureUnits($featureSlug);

            return ($used < $max) && $subscription->canUseFeature($featureSlug, 1);
        });
    }
}
