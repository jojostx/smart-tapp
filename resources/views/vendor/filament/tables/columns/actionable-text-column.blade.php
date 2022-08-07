@php
    $description = $getDescription();
    $actions = $getActions();
    $descriptionPosition = $getDescriptionPosition();
    $triggerIcon = $getTriggerIcon;
    $triggerPosition = $getTriggerPosition();
    $triggerColor = $getTriggerColor;
    $triggerLabel = $getTriggerLabel;
    $darkMode = config('filament.dark_mode');
    $tooltip = $getTooltip();
@endphp

<div 
    x-cloak 
    x-data="{ hovered: false, clicked: false, copied: false }" 
    x-on:mouseover="hovered = true" 
    x-on:mouseover.outside="hovered = false;"
    {{ $attributes->merge(
        $getExtraAttributes())->class([
            'px-4 py-3 filament-tables-text-column flex items-center justify-between gap-2',
            'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $getAction() || $getUrl(),
            'whitespace-normal' => $canWrap(),
        ]) 
    }}>

    @if ($getFormattedState() && $triggerPosition == 'before')
    <div class="relative" 
        @keyup.escape.window="clicked = false; copied = false" 
        x-on:click.outside="clicked = false; copied = false">
        <x-filament-support::icon-button
            x-on:click.prevent="$refs.panel.toggle; clicked = true"
            x-bind:class="hovered || clicked || !{{ $isAnimated() ? 'true' : 'false' }} ? 'md:opacity-100' : 'md:opacity-0'"
            :color="$triggerColor"
            :dark-mode="$darkMode"
            :icon="$triggerIcon"
            :tooltip="$tooltip"
            class="-my-2 transition duration-300 rounded-md"
            title="cell options dropdown trigger"
        >
            <x-slot name="label">
                {{ $triggerLabel }}
            </x-slot>
        </x-filament-support::icon-button>

        <div
            x-ref="panel"
            x-transition
            x-cloak
            x-float.placement.bottom-end.flip.offset.shift.teleport="{ offset: 8 }"
            @class([
                'absolute hidden z-20 shadow-xl ring-1 ring-gray-900/10 overflow-hidden rounded-md w-52 filament-action-group-dropdown',
                'dark:ring-white/20' => $darkMode,
            ])
        >
            <ul @class([
                'py-1 space-y-1 bg-white shadow rounded-md',
                'dark:bg-gray-700 dark:divide-gray-600' => $darkMode,
            ])>
                <li class="filament-tables-grouped-action">
                    <button class="flex items-center w-full h-8 px-3 text-sm font-medium group whitespace-nowrap filament-dropdown-item focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700 "
                        @click.prevent="$clipboard('{{ $getFormattedState() }}'); copied = true; $tooltip('{{ __('Copied!') }}', { placement: 'auto-end', delay: 500, onHidden: () => { copied = false }, })">

                        <x-heroicon-o-clipboard x-show="!copied" class="flex-shrink-0 w-5 h-5 mr-2 -ml-1 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white text-primary-500 dark:text-gray-400"/>
                        
                        <x-heroicon-o-check x-show="copied" class="flex-shrink-0 w-5 h-5 mr-2 -ml-1 rtl:ml-2 rtl:-mr-1 text-success-500"/>

                        <span class="truncate">
                            Copy
                        </span>

                        <span class="ml-auto text-gray-500 truncate group-hover:text-primary-300 group-focus:text-white dark:text-gray-400">
                            ctrl+C
                        </span>
                    </button>
                </li>
                
                @foreach ($actions as $action)
                    @if (! $action->isHidden())
                        {{ $action }}
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div>
        @if (filled($description) && $descriptionPosition === 'above')
            <span class="block text-sm text-gray-400">
                {!! \Illuminate\Support\Str::of($description)->markdown()->sanitizeHtml() !!}
            </span>
        @endif
        
        {{ $getFormattedState() }}
        
        @if (filled($description) && $descriptionPosition === 'below')
            <span class="block text-sm text-gray-400">
                {!! \Illuminate\Support\Str::of($description)->markdown()->sanitizeHtml() !!}
            </span>
        @endif
    </div>
    
    @if ($getFormattedState() && $triggerPosition == 'after')
    <div class="relative" 
        @keyup.escape.window="clicked = false" 
        x-on:click.outside="clicked = false">
        <x-filament-support::icon-button
            x-on:click.prevent="$refs.panel.toggle; clicked = true"
            x-bind:class="hovered || clicked || !{{ $isAnimated() ? 'true' : 'false' }} ? 'md:opacity-100' : 'md:opacity-0'"
            :color="$triggerColor"
            :dark-mode="$darkMode"
            :icon="$triggerIcon"
            :tooltip="$tooltip"
            class="-my-2 transition duration-300 rounded-md"
            title="cell options dropdown trigger"
        >
            <x-slot name="label">
                {{ $triggerLabel }}
            </x-slot>
        </x-filament-support::icon-button>

        <div
            x-ref="panel"
            x-transition
            x-cloak
            x-float.placement.bottom-end.flip.offset.shift.teleport="{ offset: 8 }"
            @class([
                'absolute hidden z-20 shadow-xl ring-1 ring-gray-900/10 overflow-hidden rounded-md w-52 filament-action-group-dropdown',
                'dark:ring-white/20' => $darkMode,
            ])
        >
            <ul @class([
                'py-1 space-y-1 bg-white shadow rounded-md',
                'dark:bg-gray-700 dark:divide-gray-600' => $darkMode,
            ])>
                <li class="filament-tables-grouped-action">
                    <button class="flex items-center w-full h-8 px-3 text-sm font-medium group whitespace-nowrap filament-dropdown-item focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700 "
                        @click.prevent="$clipboard('{{ $getFormattedState() }}'); copied = true; $tooltip('{{ __('Copied!') }}', { placement: 'auto-end', delay: 500, onHidden: () => { copied = false }, })">
                        
                        <x-heroicon-o-clipboard x-show="!copied" class="flex-shrink-0 w-5 h-5 mr-2 -ml-1 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white text-primary-500 dark:text-gray-400"/>
                        
                        <x-heroicon-o-check x-show="copied" class="flex-shrink-0 w-5 h-5 mr-2 -ml-1 rtl:ml-2 rtl:-mr-1 text-success-500"/>

                        <span class="truncate">
                            Copy
                        </span>

                        <span class="ml-auto text-gray-500 truncate group-hover:text-primary-300 group-focus:text-white dark:text-gray-400">
                            ctrl+C
                        </span>
                    </button>
                </li>
                
                @foreach ($actions as $action)
                    @if (! $action->isHidden())
                        {{ $action }}
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>