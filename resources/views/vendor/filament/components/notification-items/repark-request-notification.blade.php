@props([
  'notification',
  'darkMode' => config('filament.dark_mode')
])

@php
  $link = route('filament.resources.tenant/repark-requests.index', [ 'tableSearchQuery' => $notification->data['repark_request_uuid'] ]);
@endphp

<x-notification-card wire:key="notification-{{ $notification->id }}" x-bind:class="isNotificationRead('{{ $notification->id }}') ? 'bg-white {{ $darkMode ? 'dark:bg-gray-500/10' : '' }}' : 'bg-primary-50 {{ $darkMode ? 'dark:bg-primary-500/10' : '' }}' " class="border-b border-gray-300 dark:border-gray-700">
  <x-slot:icon>
    <div tabindex="0" aria-label="group icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border rounded-full text-danger-500 border-danger-300 focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5" height="192" fill="currentColor" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><path d="M240,112H229.2L201.4,49.5A15.9,15.9,0,0,0,186.8,40H69.2a15.9,15.9,0,0,0-14.6,9.5L26.8,112H16a8,8,0,0,0,0,16h8v80a16,16,0,0,0,16,16H64a16,16,0,0,0,16-16V192h96v16a16,16,0,0,0,16,16h24a16,16,0,0,0,16-16V128h8a8,8,0,0,0,0-16ZM80,160H64a8,8,0,0,1,0-16H80a8,8,0,0,1,0,16Zm96,0a8,8,0,0,1,0-16h16a8,8,0,0,1,0,16Z"></path></svg>
    </div>
  </x-slot>
  
  <x-slot:link x-on:click="markNotificationAsRead('{{ $notification->id }}')">
    {{ $link }}
  </x-slot>
  
  <x-slot:title>
    {{ $notification->data['title'] }}
  </x-slot>
  
  <x-slot:body>
    {!! $notification->data['content'] !!}
  </x-slot>

  <x-slot:timestamp>
    {{ $notification->created_at->diffForHumans() }}
  </x-slot>

  <x-slot:action>
    <div>
      <template x-ref="template">
        <div class="divide-y">
          <button type="button" @click="toggleNotificationRead('{{ $notification->id }}'); $refs.panel.open;" class="flex items-center w-full h-8 px-3 text-sm font-medium filament-dropdown-item group whitespace-nowrap focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700">
            <span x-text="isNotificationRead('{{ $notification->id }}') ? 'Mark As Unread' : 'Mark As Read' ">
              Mark As Read
            </span>
          </button>
          <button type="button" class="flex items-center w-full h-8 px-3 text-sm font-medium filament-dropdown-item group whitespace-nowrap focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700">
            <span class="">
              Delete
            </span>
          </button>
        </div>
      </template>

      <x-filament-support::icon-button :dark-mode="$darkMode" icon="heroicon-o-dots-vertical" class="-my-2" x-tooltip.on.click="{
              content: () => $refs.template.innerHTML,
              allowHTML: true,
              interactive: true,
              appendTo: $root,
              placement: 'bottom-start',
              theme: 'light',
        }">
        <x-slot name="label">
          trigger
        </x-slot>
      </x-filament-support::icon-button>
    </div>
  </x-slot>
</x-notification-card>
