<x-filament::card>
  <div class="flex items-center justify-between gap-8">
      <div @class([
          'flex items-center space-x-2 rtl:space-x-reverse text-sm font-medium text-gray-500',
          'dark:text-gray-200' => config('filament.dark_mode'),
      ])>
          @if ($icon = $getIcon())
              <x-dynamic-component :component="$icon" class="w-4 h-4" />
          @endif

          <span>{{ $getLabel() }}</span>
      </div>
  </div>

  <x-filament::hr />

  <div class="flex flex-col">
      <div class="flex items-center justify-center">
          <div 
            @class([
                'flex items-center justify-center rounded-full',
                match ($getSize()) {
                    'sm' => 'w-32 h-32',
                    'md' => 'w-48 h-48',
                    'lg' => 'w-72 h-72',
                    'xl' => 'w-96 h-96',
                },
            ]) 
            style="background: conic-gradient({{ $getStylesBackground() }})"
          >
              @if ($shouldShowTotalLabel())
              <div @class([
                  'flex items-center justify-center rounded-full',
                  'bg-white dark:bg-gray-800',
                  match ($getSize()) {
                      'sm' => 'w-24 h-24',
                      'md' => 'w-36 h-36',
                      'lg' => 'w-52 h-52',
                      'xl' => 'w-72 h-72',
                  },
              ])>
                  <span @class([
                      'text-gray-600 dark:text-gray-200',
                      match ($getSize()) {
                          'sm' => 'text-sm',
                          'md' => 'text-base',
                          'lg' => 'text-lg',
                          'xl' => 'text-xl',
                      },
                  ])>
                      {{ $getTotalValue() }}
                  </span>
              </div>
              @endif
          </div>
      </div>

      <div class="flex flex-col mt-6 divide-y divide-gray-200 dark:divide-gray-700">
          @foreach ($getCachedData() as $slice)
            {{ $slice }}
          @endforeach
      </div>
  </div>
</x-filament::card>
