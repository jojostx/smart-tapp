<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Repositories\PlanRepository;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Jojostx\Larasubs\Models\Plan;

class CheckoutController extends Controller
{
    /**
     * The plan repository instance.
     */
    protected PlanRepository $planRepository;

    protected function planRepository(): PlanRepository
    {
        return $this->planRepository ??= app(PlanRepository::class);
    }

    public function index(Request $request)
    {
        $plans = $this->planRepository()->getActive();

        // retrieve the tenant id from the query_string, and retrieve the tenant from the db,
        $tenant = tenancy()->find($request->get('tenant', ''));

        $selected_plan = $this->planRepository()->getActiveBySlug($request->get('plan', ''));

        return \view('subscriptions.checkout', compact('plans', 'selected_plan', 'tenant'));
    }

    public function create(Request $request)
    {
        \dd('created', $request);
        return 'created';
    }

    public function update(Request $request)
    {
        return 'updated';
    }
}
