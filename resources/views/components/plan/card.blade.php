@php
$classes = $shouldHighlight ?
'border-2 border-primary-500 bg-primary-500/30 hover:shadow-primary-500/20 hover:border-primary-500/90 '
: 'border border-gray-600 hover:shadow-primary-500/10 hover:border-primary-500/10 ';
@endphp

<div class="flex flex-col shadow-xl rounded-xl p-4 transition {{ $classes }}">
    @if ($icon = $getIcon())
    <x-dynamic-component :component="$icon" class="w-8 h-8 text-primary-500" />
    @endif

    <h3 class="mt-4 text-lg font-semibold leading-6 text-white capitalize">{{ $plan->name }}</h3>

    <div class="mt-4 text-white">
        <span class="text-2xl font-bold">{{ $getCurrencySymbol() }}</span>
        <span class="text-3xl font-bold">{{ number_format($getPricePerInterval()) }}</span>

        @unless ($plan->isFree())
            <span class="text-sm text-gray-400">/{{ str_ireplace('month', 'mon', $getIntervalType()) }}</span>

            @if($plan->interval == 12)
                <span class="block text-xs text-gray-300">Billed annually</span>
            @endif
            @if($plan->interval == 6)
                <span class="block text-xs text-gray-300">Billed biannually</span>
            @endif
            @if($plan->interval == 1)
                <span class="block text-xs text-gray-300">Billed monthly</span>
            @endif
        @endunless
    </div>

    @if ($tag = $getTag())
    <p class="mt-4 text-sm leading-normal text-gray-300">
        {{ $tag }}
    </p>
    @endif

    <ul class="my-4 text-gray-400 grow">
        @foreach ($plan->features as $feature)
        <li class="flex items-center mb-2 text-sm">
            <svg class="w-3 h-3 mr-3 fill-current text-primary-400 shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
            </svg>
            <span>{{ $getFeatureTag($feature) }}</span>
        </li>
        @endforeach
    </ul>

    <div class="mt-auto text-center">
        <a href="{{ $getRoute() }}" class="flex items-center justify-center w-full py-3 text-sm font-medium text-white uppercase rounded-lg bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300">
            {{ $routeLabel }}
        </a>
    </div>
</div>
