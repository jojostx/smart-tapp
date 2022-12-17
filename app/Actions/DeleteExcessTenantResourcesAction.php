<?php

namespace App\Actions;

use App\Enums\Models\FeatureResources;
use App\Models\Tenant;
use Jojostx\Larasubs\Models\Subscription;

class DeleteExcessTenantResourcesAction
{
    protected ?Tenant $tenant;

    public function handle(Subscription $subscription, string $featureSlug = ''): ?Subscription
    {
        // delete excess resources
        if (filled($featureSlug) && $subscription->hasFeature($featureSlug)) {
            $this->deleteResource($subscription, $featureSlug);
        } else {
            foreach ($subscription->features as $feature) {
                $this->deleteResource($subscription, $feature->slug);
            }
        }

        return $subscription;
    }

    public function deleteResource(Subscription $subscription, string $featureSlug)
    {
        $remainingUnits = \tenancy()->central(function () use ($subscription, $featureSlug) {
            return $subscription->getRemainingUnitsForFeature($featureSlug);
        });

        if ($remainingUnits < 0) {
            try {
                $resource = FeatureResources::getResourceByFeature($featureSlug);

                if (filled($resource)) {
                    /** @var \Illuminate\Database\Eloquent\Model */
                    $model = resolve($resource);

                    // delete by updated_at until a max of $remainingUnits.
                    $model::query()->oldest('updated_at')->limit(abs($remainingUnits))->delete();
                }
            } catch (\Throwable $th) {
            }
        }
    }
}
