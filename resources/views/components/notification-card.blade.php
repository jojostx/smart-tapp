@props([
    'icon' => null,
    'iconColor' => 'primary',
    'color' => 'primary',
    'darkMode' => false,
    'title' => null,
    'body' => '',
    'close_action' => null,
    'actions' => [],
])

<div
    {{ $attributes->class([
        'flex flex-shrink-0 justify-between w-full p-3 my-2 filament-notifications-notification pointer-events-auto',
        'dark:bg-gray-800' => $darkMode,
      ])
    }}
>
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
                {{ \Illuminate\Support\Str::of($body)->markdown()->sanitizeHtml()->toHtmlString() }}
            </x-notifications::body>
        @endif

        @if ($actions)
            <x-notifications::actions :actions="$actions" />
        @endif
    </div>

    @if (filled($close_action))
      {{ $close_action }}
    @else
      <div class="text-gray-400 place-self-center">
        <x-filament::icon-button x-on:click="close" :dark-mode="$darkMode" :icon="'heroicon-o-x'" title="close" class="" :size="'sm'">
          <x-slot name="label">
            close
          </x-slot>
        </x-filament::icon-button>
      </div>
    @endif
</div>
