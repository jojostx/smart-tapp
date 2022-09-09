@php
    $darkMode = config('filament.dark_mode');
@endphp

<div 
  x-data="{
        navOpen: false,

        tooltip: { content: 'Notifications', theme: Alpine.store('theme') === 'light' ? 'dark' : 'light', placement: 'bottom' },

        isLoading: false,

        readNotifications: @js($readNotifications),

        init: function () {
          window.addEventListener('notification-recieved', () => { new Audio(`{{ url('assets/notification-sound.mp3') }}`).play() });
        },

        markNotificationAsRead: async function (key) {
          if (this.isNotificationRead(key)) {
              return
          }

          await $wire.markNotificationAsRead(key)
          this.readNotifications.push(key)
        },

        markNotificationAsUnread: async function (key) {
          let index = this.readNotifications.indexOf(key)

          if (index === -1) {
              return
          }

          await $wire.markNotificationAsUnread(key)
          this.readNotifications.splice(index, 1)
        },

        markAllNotificationAsRead: async function () {
            this.isLoading = true

            this.readNotifications = (await $wire.markAllNotificationsAsRead()).map((key) => key.toString())

            console.log(this.readNotifications)

            this.isLoading = false
        },

        toggleNotificationRead: async function (key) {
            if (this.isNotificationRead(key)) {
                await this.markNotificationAsUnread(key)

                return
            }

            await this.markNotificationAsRead(key)
        },

        isNotificationRead: function (key) {
            return this.readNotifications.includes(key)
        },
    }"
  wire:poll.30s
  x-on:click.outside="navOpen = false" 
  class="relative ml-4"
  >
  <button x-tooltip.html="tooltip" @click="$refs.panel.toggle; navOpen = ! navOpen;" :class="navOpen ? 'text-primary-500 bg-primary-500/10' : ''" title="Notifications Trigger" type="button" class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-center text-gray-500 rounded-md hover:text-gray-900 dark:hover:text-white dark:text-gray-400 filament-icon-button hover:bg-gray-500/5 focus:outline-none focus:text-primary-500 focus:bg-primary-500/10" aria-expanded="false" aria-controls="panel-1uT4AAWg">
    <span class="sr-only">
      Notifications
    </span>

    <x-heroicon-s-bell class="w-6 h-6 filament-icon-button-icon" />

    @if ($this->hasUnreadNotifications)
    <span class="absolute block w-3 h-3 border-2 border-white rounded-full bg-danger-500 top-1 right-2 dark:border-gray-900"></span>
    @endif
  </button>

  <!-- Dropdown menu -->
  <div x-ref="panel" x-transition="" x-float.placement.bottom-end.offset.shift="{ offset: 10 }" style="position: fixed;" id="panel-1uT4AAWg" class="absolute z-20 hidden w-full max-w-md overflow-hidden bg-white rounded-md shadow-xl filament-action-group-dropdown ring-1 ring-gray-900/10 dark:bg-gray-700" aria-modal="true" role="dialog">
    <div class="flex items-center justify-between w-full px-4 py-3 border-b-2 border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
      <p class="font-medium text-gray-700 dark:text-white">
        Notifications
      </p>
      
      @if ($this->hasUnreadNotifications)
      <x-filament::link tag="button" size="sm" x-on:click="markAllNotificationAsRead" style="font-size: 0.75rem; line-height: 1rem;">
        {{ __('Mark all as Read') }}
      </x-filament::link>
      @endif
    </div>

    <div class="pb-3 overflow-y-auto text-sm text-gray-700 h-72 dark:text-gray-200">
      @forelse ($notifications as $notification)
        <x-filament::notification-items.repark-request-notification :notification="$notification"/>
        @if ($loop->last)
          <div class="flex items-center justify-between px-3">
            <hr class="w-full">
            <p tabindex="0" class="flex flex-shrink-0 px-3 py-8 text-sm leading-normal text-gray-500 focus:outline-none">Thats it for now :)</p>
            <hr class="w-full">
          </div>
        @endif
      @empty
        <div class="flex flex-col items-center justify-center h-full text-center">
          <p class="flex flex-shrink-0 px-3 text-xl font-semibold leading-normal text-gray-500 focus:outline-none">
            No Notifications
          </p>
          <p class="flex flex-shrink-0 px-3 text-sm leading-normal text-gray-500 focus:outline-none">
            You'll be notified when something arrives.
          </p>
        </div>
      @endforelse
    </div>

    @if ($this->hasUnreadNotifications)
      <a href="#" class="flex items-center justify-center p-3 text-sm font-medium text-gray-900 border-t-2 border-gray-200 bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
        <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
          <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
        View all
      </a>
    @endif
  </div>
</div>