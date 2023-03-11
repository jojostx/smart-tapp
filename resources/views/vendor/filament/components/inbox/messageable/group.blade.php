@props([
    'label',
    'messageables',
    'selectedMessageable',
    'wireClickEvent',
    'sum',
    'iconComponent'
])

@php
    $iconComponent= $label == 'admins' ? 'heroicon-o-user' : 'heroicon-o-support';
@endphp

<div class="flex flex-col">
    <button
        @click="activeMenu = activeMenu == '{{ $label }}' ? false : '{{ $label }}'" 
        :class="{ 'button-active': activeMenu === '{{ $label }}' }" 
        class="flex items-center justify-between px-4 py-4 text-xs border-b">
        <span class="flex items-center gap-2 font-bold">
            <x-dynamic-component :component="$iconComponent" class="w-4 h-4 shrink-0"/>
            <span class="capitalize">{{ $label }}</span>
            
            @if($sum)
                <span class="flex items-center justify-center w-4 h-4 ml-auto text-xs font-medium leading-none text-white rounded-full bg-primary-500">
                    {{ $sum }}
                </span>
            @endif
        </span>

        <span class="flex items-center justify-center w-5 h-5 text-gray-600">
            <x-heroicon-o-chevron-down x-bind:class="{ 'rotate-180' : activeMenu === '{{ $label }}' }" class="transition-all duration-300 origin-center" />
        </span>
    </button>
    <ul 
        x-show="activeMenu === '{{ $label }}'" 
        x-cloak 
        x-collapse 
        class="flex flex-col h-full px-2 py-2 space-y-1 overflow-y-scroll border-b max-h-48">
        @foreach ($messageables as $messageable)
            <x-filament::inbox.messageable.item 
                :$messageable 
                :is-selected="$selectedMessageable?->is($messageable) ?? false" 
                :$wireClickEvent
            />
        @endforeach
    </ul>
</div>