<?php

namespace App\Filament\Components;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class SubscriptionIndicator extends Component
{
    protected static string $view = 'filament::components.subscription-indicator';

    public function getSubscriptionProperty()
    {
        /** @var \App\Models\Tenant */
        $tenant = \tenant();

        return $tenant?->subscription;
    }

    public function render(): View
    {
        return view(static::$view);
    }
}
