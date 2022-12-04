@php
    $classes = $is_highlighted() ? 
    'border-2 border-primary-500 bg-primary-500/30 hover:shadow-primary-500/20 hover:border-primary-500/90 ' 
    : 'border border-gray-600 hover:shadow-primary-500/10 hover:border-primary-500/10 ';
@endphp

<div class="shadow-xl rounded-xl block p-8 transition {{ $classes }}">
    @if ($icon = $getIcon())
    <x-dynamic-component :component="$icon" class="w-10 h-10 text-primary-500" />
    @endif

    <h3 class="mt-4 text-xl font-bold text-white capitalize">{{ $plan->name }}</h3>

    <div class="mt-4 text-white">
        <span class="text-[2rem] font-bold">{{ $getCurrencySymbol() }}</span>
        <span class="font-bold text-[2.625rem]">{{ number_format($getPricePerInterval()) }}</span>
        <span class="text-gray-400">per {{ $getIntervalType() }}</span>
        <span class="block text-sm text-gray-400">Billed annually</span>
    </div>

    @if ($tag = $getTag())
    <p class="mt-4 text-gray-300">
        {{ $tag }}
    </p>
    @endif

    <ul class="mt-4 -mb-2 text-gray-400 grow">
        @foreach ($plan->features as $feature)
            <li class="flex items-center mb-2">
                <svg class="w-3 h-3 mr-3 fill-current text-primary-400 shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                </svg>
                <span>{{ $getFeatureTag($feature) }}</span>
            </li>
        @endforeach
    </ul>

    <div class="mt-12 text-center">
        <a href="{{ route('register') }}?plan={{ $plan->slug }}" class="flex items-center justify-center w-full py-3 text-sm font-medium text-white uppercase rounded-lg bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300">
            Start now
        </a>
    </div>
</div>