<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Models\FeatureResources;
use App\Enums\Models\UserAccountStatus;
use App\Filament\Resources\UserResource;
use App\Models\Tenant;
use App\Models\Tenant\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jojostx\Larasubs\Models\Subscription;
use Spatie\Permission\PermissionRegistrar;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function afterCreate(): void
    {
        if ($this->record instanceof User) {
            $this->record->sendCreateNewPasswordNotification();

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        /** @var Tenant */
        $tenant = \tenant();

        \tenancy()->central(function () use ($tenant) {
            /** @var Subscription */
            $subscription = $tenant->subscription;
            $featureSlug = FeatureResources::TEAM_MEMBERS->value;

            if ($subscription->missingFeature($featureSlug));

            return false;

            $feature = $subscription->plan->getFeatureBySlug($featureSlug);

            $subscription->useUnitsOnFeature($feature, 1);
        });
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Str::random(40);
        $data['status'] = UserAccountStatus::INACTIVE;

        return $data;
    }

    public function beforeCreate()
    {
        if (! $this->canCreateUser()) {
            Notification::make()
                ->title('Unable to create Admin User')
                ->body('You have reached the maximum team member allocation for your current subscription and can not create any more admins. **Consider upgrading your plan**')
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages(['parking_lot_id' => __('Unable to create User')]);
        }
    }

    /**
     * check if user count has reached limit for subscription
     *
     * @return bool
     */
    public function canCreateUser()
    {
        $tenant = \tenant();
        $used = User::count();

        return tenancy()->central(function () use ($tenant, $used) {
            /** @var Subscription */
            $subscription = $tenant->subscription;
            $featureSlug = FeatureResources::TEAM_MEMBERS->value;

            if ($subscription->missingFeature($featureSlug));

            return false;

            $max = $subscription->getMaxFeatureUnits($featureSlug);

            return ($used < $max) && $subscription->canUseFeature($featureSlug, 1);
        });
    }
}
