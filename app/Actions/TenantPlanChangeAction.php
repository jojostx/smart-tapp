<?php

namespace App\Actions;

use App\Models\Tenant;
use App\Repositories\PlanRepository;
use Jojostx\Larasubs\Models\Subscription;
use Jojostx\Larasubs\Models\Plan;

class TenantPlanChangeAction
{
  protected ?Tenant $tenant;
  protected ?Plan $plan;

  public function __construct(
    protected PlanRepository $planRepository,
    protected DeleteExcessTenantResources $deleteExcessTenantResources
  ) {
  }

  // if plan is free and the tenant has no subscription
  // or the tenant has a higher subscription plan, perform the plan downgrade action.
  // change plan for current subscription and allow sync
  public function handle(Tenant $tenant, Plan $plan, ?Subscription $subscription = null): ?Subscription
  {
    $subscription ??= $tenant->subscription;
    
    $subscription = \tenancy()->central(function () use ($subscription, $plan, $tenant)
    {
      // if no sub is passed perform the action on the tenants' most recent sub
      if (blank($subscription)) {
        return $tenant->subscribeTo($plan);
      } else {
        // change plan and delete excess resource
        return $subscription->changePlan($plan);
      }
    });

    // delete excess resources
    return $this->deleteExcessTenantResources->handle($subscription);
  }
}
