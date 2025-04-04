<div {{ $attributes->class(['filament-inbox-search-input']) }}>
    <label for="inboxSearchInput" class="sr-only">
        {{ __('search') }}
    </label>

    <div class="relative group max-w-md">
        <span @class([
            'absolute inset-y-0 left-0 flex items-center justify-center w-10 h-10 text-gray-500 pointer-events-none group-focus-within:text-primary-500',
            'dark:text-gray-400' => config('filament.dark_mode'),
        ])>
            <x-heroicon-o-search class="w-5 h-5" wire:loading.remove.delay wire:target="search" />

            <x-filament-support::loading-indicator class="w-5 h-5" wire:loading.delay wire:target="search" />
        </span>

        <input
            wire:model.debounce.500ms="search"
            id="inboxSearchInput"
            placeholder="{{ __('Search') }}"
            type="search"
            autocomplete="off"
            @class([
                'block w-full h-10 pl-10 bg-gray-400/10 placeholder-gray-500 text-gray-800 border border-gray-300 transition duration-75 rounded-lg focus:bg-white focus:placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500',
                'dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400' => config('filament.dark_mode'),
            ])
        >
    </div>
</div>
