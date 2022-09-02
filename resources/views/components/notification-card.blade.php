@props([
    'icon' => null,
    'iconColor' => 'primary',
    'color' => 'primary',
    'darkMode' => false,
    'title' => null,
    'body' => null,
    'action' => null,
    'timestamp' => null,
    'link' => null,
])

<div
    {{ $attributes->class([
        'flex flex-shrink-0 justify-between w-full filament-notifications-notification pointer-events-auto ',
        'dark:bg-gray-800 ' => $darkMode,
        'hover:bg-primary-100 dark:hover:bg-gray-700 ' => $link,
      ])
    }}
>
    @if (filled($link))
      <a href="{{ $link }}" {{ $link->attributes->class(['flex p-4 pr-0']) }}>
        @if ($icon)
            @if (is_string($icon))
              <x-notifications::icon :icon="$icon" :color="$iconColor" />
            @else
              {{ $icon }}
            @endif
        @endif

        <div class="grid flex-1 flex-shrink-0 ml-3 mr-2">
            @if ($title)
                <x-notifications::title>
                    {{ \Illuminate\Support\Str::of($title)->markdown()->sanitizeHtml()->toHtmlString() }}
                </x-notifications::title>
            @endif
    
            @if ($body)
              <x-notifications::body>
                <div class="text-gray-700">
                  {{ \Illuminate\Support\Str::of($body)->markdown()->sanitizeHtml()->toHtmlString() }}
                </div>
              </x-notifications::body>
            @endif
    
            @if ($timestamp)
                <div class="mt-2 text-xs leading-3 text-primary-700">
                  {{ \Illuminate\Support\Str::of($timestamp)->markdown()->sanitizeHtml()->toHtmlString() }}
                </div>
            @endif
        </div>
      </a>
    @else
      <div class="flex p-4 pr-0">
        @if ($icon)
            @if (is_string($icon))
              <x-notifications::icon :icon="$icon" :color="$iconColor" />
            @else
              {{ $icon }}
            @endif
        @endif

        <div class="grid flex-1 flex-shrink-0 ml-3 mr-2">
            @if ($title)
              <x-notifications::title>
                  {{ \Illuminate\Support\Str::of($title)->markdown()->sanitizeHtml()->toHtmlString() }}
              </x-notifications::title>
            @endif
    
            @if ($body)
              <x-notifications::body>
                <div class="text-gray-700">
                  {{ \Illuminate\Support\Str::of($body)->markdown()->sanitizeHtml()->toHtmlString() }}
                </div>
              </x-notifications::body>
            @endif
    
            @if ($timestamp)
              <div 
                @class([
                    'filament-notifications-timestamp mt-2 text-xs leading-3 text-primary-700',
                    'dark:text-gray-700' => $darkMode,
                ])
              >
                {{ \Illuminate\Support\Str::of($timestamp)->markdown()->sanitizeHtml()->toHtmlString() }}
              </div>
            @endif
        </div>
      </div>
    @endif

    <div class="py-4 pr-4 text-gray-400">
      @if (filled($action))
        {{ $action }}
      @else
        <x-filament::icon-button x-on:click="close" :dark-mode="$darkMode" :icon="'heroicon-o-x'" title="close" class="" :size="'sm'">
          <x-slot name="label">
            close
          </x-slot>
        </x-filament::icon-button>
      @endif
    </div>
</div>


