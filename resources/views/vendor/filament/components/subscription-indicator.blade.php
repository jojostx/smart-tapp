@php
    $tooltip = match (true) {
        blank($this->subscription) => 'You have not subscribed to a plan, please subscribe.',
        $this->subscription->isOverdue() => 'Your subscription has ended, please renew',
        $this->subscription->isOnGracePeriod() => 'Please renew your subscription, it will end on' . $this->grace_ends_at->diffForHumans(),
        $this->subscription->isCancelled() => 'Auto-renewal has been cancelled for your subscription',
        default => null,
    };

    $icon = "heroicon-o-x";    
@endphp

<div class="text-xs">
    @if(filled($tooltip))    
        <div class="mr-1 text-xs cursor-pointer" x-data="{ tooltip: '{{ $tooltip }}' }">
            <button x-tooltip="tooltip">
            <x-dynamic-component :component="$icon" class="w-8 h-8 text-primary-500" />
            </button>
        </div>
    @endif

    @if(blank($this->subscription = null))
    <p>
        You have not subscribed to a plan, please subscribe.
    </p>
    @elseif($this->subscription->isOverdue())
    <p>
        Your subscription has ended, please renew.
    </p>
    @elseif($this->subscription->isEnded() && filled($ends_at = $this->subscription->grace_ends_at))
    <p>
        Please renew your subscription, it will end on {{$ends_at->diffForHumans()}}.
    </p>
    @elseif($this->subscription->isCancelled())
    <p>
        Auto-renewal has been cancelled for your subscription.
    </p>
    @endif
</div>
