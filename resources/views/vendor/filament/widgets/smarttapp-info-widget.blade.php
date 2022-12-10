<x-filament::widget class="smarttapp-info-widget">
    <x-filament::card class="relative">
        <div class="relative flex flex-col items-center justify-center h-12 space-y-2">
            <div class="space-y-1">
                <a
                    href="https://smart-tapp.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    @class([
                        'flex items-end space-x-2 rtl:space-x-reverse text-gray-800 hover:text-primary-500 transition',
                        'dark:text-primary-500 dark:hover:text-primary-400' => config('filament.dark_mode'),
                    ])
                >
                  <x-logo class="flex-shrink-0 w-auto text-primary-600 h-9" alt="{{ config('app.name') }} Logo" />
                  <span class="self-center text-xl font-semibold whitespace-nowrap">{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="flex space-x-2 text-sm rtl:space-x-reverse">
                <a
                    href="https://smart-tapp.com#faqs"
                    target="_blank"
                    rel="noopener noreferrer"
                    @class([
                        'text-gray-600 hover:text-primary-500 focus:outline-none focus:underline',
                        'dark:text-gray-300 dark:hover:text-primary-500' => config('filament.dark_mode'),
                    ])
                >
                    {{ __('Faqs') }}
                </a>

                <span>
                    &bull;
                </span>

                <a
                    href="https://smart-tapp.com#help"
                    target="_blank"
                    rel="noopener noreferrer"
                    @class([
                        'text-gray-600 hover:text-primary-500 focus:outline-none focus:underline',
                        'dark:text-gray-300 dark:hover:text-primary-500' => config('filament.dark_mode'),
                    ])
                >
                    {{ __('Help') }}
                </a>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
