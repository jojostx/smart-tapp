@props([
    'label',
    'messageables',
    'selectedMessageable',
    'wireClickEvent',
    'sum',
    'iconComponent'
])

@php
    $iconComponent = $label == 'admins' ? 'heroicon-o-user' : 'heroicon-o-support';
@endphp

<div class="flex flex-col">
    <button
        @click="activeMenu = activeMenu === '{{ $label }}' ? false : '{{ $label }}'" 
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
    @if (filled($messageables))
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
    @else
    <div
        x-show="activeMenu === '{{ $label }}'" 
        x-cloak 
        x-collapse
        class="p-2 space-y-2 bg-white border-b">
        <div class="flex flex-col items-center justify-center flex-1 p-6 mx-auto space-y-6 text-center bg-white">
          <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-50 text-primary-500">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>

          <div class="max-w-md space-y-1">
            <h2 class="text-xl font-bold tracking-tight">
              No {{ $label }} found
            </h2>
          </div>
        </div>
    </div>
    @endif
</div>