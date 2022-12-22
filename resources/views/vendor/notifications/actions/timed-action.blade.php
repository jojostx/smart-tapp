@php
    $wireClickAction = null;
    $maxDuration = 30000;
    $duration = isset($action->getExtraAttributes()['duration']) && $action->getExtraAttributes()['duration'] < $maxDuration ? $action->getExtraAttributes()['duration'] : $maxDuration;

    if ($action->getEvent()) {
        $emitArguments = collect([$action->getEvent()])
            ->merge($action->getEventData())
            ->map(fn (mixed $value) => \Illuminate\Support\Js::from($value)->toHtml())
            ->implode(', ');

        $wireEmitAction = "\$wire.emit($emitArguments)";
    }
@endphp

<div x-init="$nextTick(() => { setTimeout(() => { {!! $action->isEnabled() ? $wireEmitAction : 'disabled'; !!}; {!! $action->isEnabled() && $action->shouldCloseNotification() ? 'close()' : null !!} }, {{ $duration }} ) })" class="sr-only filament-notifications-timed-action">
    <span>{{ $getLabel() }}</span>
</div>