<div {{ $attributes->class([
    'filament-inbox-search-no-results-message text-gray-600 px-6 py-4',
    'dark:text-gray-200' => config('filament.dark_mode'),
]) }}>
    {{ __('No Results Found') }}
</div>
