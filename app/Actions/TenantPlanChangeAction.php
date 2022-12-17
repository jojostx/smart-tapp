<?php

namespace App\Actions;

use App\Models\Tenant;
use Jojostx\Larasubs\Models\Plan;
use Jojostx\Larasubs\Models\Subscription;

class TenantPlanChangeAction
{
    public function __construct(
        protected DeleteExcessTenantResourcesAction $deleteExcessTenantResourcesAction
    ) {
    }

    // if plan is free and the tenant has no subscription
    // or the tenant has a higher subscription plan, perform the plan downgrade action.
    // change plan for current subscription and allow sync
    public function handle(Tenant $tenant, Plan $plan, ?Subscription $subscription = null): ?Subscription
    {
        $subscription ??= $tenant->subscription;

        $subscription = \tenancy()->central(function () use ($subscription, $plan, $tenant) {
            // if no sub is passed perform the action on the tenants' most recent sub
            if (blank($subscription)) {
                return $tenant->subscribeTo($plan, withoutTrial: true);
            } else {
                // change plan and delete excess resource
                return $subscription->changePlan($plan);
            }
        });

        // delete excess resources
        return $this->deleteExcessTenantResourcesAction->handle($subscription);
    }
}
