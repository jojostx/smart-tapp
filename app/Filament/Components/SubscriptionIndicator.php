<?php

namespace App\Filament\Components;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class SubscriptionIndicator extends Component
{
    protected static string $view = 'filament::components.subscription-indicator';

    protected function getListeners()
    {
        return ['refresh' => '$refresh'];
    }

    public function getSubscriptionProperty()
    {
        /** @var \App\Models\Tenant */
        $tenant = \tenant();

        return $tenant?->subscription;
    }

    public function getTooltipProperty()
    {
        $subscription = $this->subscription;
        $grace_ends_at = $subscription?->grace_ends_at?->diffForHumans();

        return match (true) {
            blank($subscription) => 'You have not subscribed to a plan, please subscribe.',
            $subscription->isOverdue() => 'Your subscription has ended, please renew it to continue using our services',
            $subscription->isOnGracePeriod() => 'Please renew your subscription, it will end ' . $grace_ends_at,
            $subscription->isCancelled() => 'Auto-renewal has been cancelled for your subscription',
            default => null,
        };
    }

    public function getIconProperty()
    {
        $subscription = $this->subscription;

        return match (true) {
            blank($subscription) => 'heroicon-o-exclamation',
            $subscription->isOverdue() => 'heroicon-o-exclamation-circle',
            $subscription->isOnGracePeriod() => 'heroicon-o-exclamation-circle',
            $subscription->isCancelled() => 'heroicon-o-exclamation',
            default => 'heroicon-o-exclamation',
        };
    }

    public function getColorProperty()
    {
        $subscription = $this->subscription;

        return match (true) {
            blank($subscription) => 'warning',
            $subscription->isOverdue() => 'danger',
            $subscription->isOnGracePeriod() => 'warning',
            $subscription->isCancelled() => 'warning',
            default => 'primary',
        };
    }

    public function render(): View
    {
        return view(static::$view);
    }
}
