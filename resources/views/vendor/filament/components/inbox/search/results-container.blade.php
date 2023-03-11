@props([
    'results',
    'wireClickEvent'
])

<div
    x-data="{ isOpen: true }"
    x-show="isOpen"
    x-on:keydown.escape.window="isOpen = false; $wire.set('search', '')"
    x-on:click.outside="isOpen = false; $wire.set('search', '')"
    x-on:open-inbox-search-results.window="isOpen = true"
    {{ $attributes->class(['filament-inbox-search-results-container w-full absolute right-0 rtl:right-auto rtl:left-0 top-auto z-10 mt-2 shadow-xl overflow-hidden rounded-xl']) }}
>
    <div @class([
        'overflow-y-scroll overflow-x-hidden max-h-96 w-full bg-white shadow rounded-xl',
        'dark:bg-gray-800' => config('filament.dark_mode'),
    ])>
        @forelse ($results->getCategories() as $group => $groupedResults)
            <x-filament::inbox.search.result-group :label="$group" :results="$groupedResults" :$wireClickEvent/>
        @empty
            <x-filament::inbox.search.no-results-message />
        @endforelse
    </div>
</div>