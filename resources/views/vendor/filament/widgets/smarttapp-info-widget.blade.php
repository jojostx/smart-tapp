<x-filament::widget class="filament-account-widget">
    <x-filament::card>
        @php
        $user = \Filament\Facades\Filament::auth()->user();
        @endphp

        <div class="flex items-center h-12 space-x-4 rtl:space-x-reverse">
            <a href="{{ config('app.url') }}" target="_blank" rel="noopener noreferrer">
                <x-logo class="flex-shrink-0 w-auto h-12 text-primary-600" alt="{{ config('app.name') }} Logo" />
            </a>

            <div>
                <a href="{{ config('app.url') }}" target="_blank" rel="noopener noreferrer" @class([ 'text-lg font-bold tracking-tight sm:text-xl text-gray-800 hover:text-primary-500 transition flex items-end space-x-2 rtl:space-x-reverse ' , 'dark:text-primary-500 dark:hover:text-primary-400'=> config('filament.dark_mode')])>
                    {{ config('app.name') }}
                </a>

                <div class="text-sm">
                    <a href="{{ config('app.url') }}#faqs" target="_blank" rel="noopener noreferrer" @class(['text-gray-600 hover:text-primary-500 focus:outline-none focus:underline' , 'dark:text-gray-300 dark:hover:text-primary-500'=> config('filament.dark_mode')])>
                        {{ __('Faqs') }}
                    </a>

                    <span>
                        &bull;
                    </span>

                    <a href="{{ config('app.url') }}#help" target="_blank" rel="noopener noreferrer" @class(['text-gray-600 hover:text-primary-500 focus:outline-none focus:underline' , 'dark:text-gray-300 dark:hover:text-primary-500'=> config('filament.dark_mode')])>
                        {{ __('Help') }}
                    </a>
                </div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
