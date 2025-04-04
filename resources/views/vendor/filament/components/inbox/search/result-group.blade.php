@props([
    'label',
    'results',
    'wireClickEvent'
])

<ul {{ $attributes->class([
    'filament-inbox-search-result-group divide-y',
    'dark:divide-gray-700' => config('filament.dark_mode'),
]) }}>
    <li class="sticky top-0 z-10">
        <header @class([
            'px-6 py-2 bg-gray-50/80 backdrop-blur-xl backdrop-saturate-150',
            'dark:bg-gray-700' => config('filament.dark_mode'),
        ])>
            <p @class([
                'text-xs font-bold tracking-wider text-gray-500 uppercase',
                'dark:text-gray-400' => config('filament.dark_mode'),
            ])>
                {{ $label }}
            </p>
        </header>
    </li>

    @foreach ($results as $result)
        <x-filament::inbox.search.result
            :details="$result->details"
            :title="$result->title"
            :identifier="$result->identifier"
            :$wireClickEvent
        />
    @endforeach
</ul>
