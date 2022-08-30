<div x-data class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
  <div>
    <div class="relative flex flex-col items-center justify-center overflow-hidden">
      <x-logo class="flex-shrink-0 w-auto mb-2 mr-3 text-primary-600 h-9" alt="{{ config('app.name') }} Logo" />
      <span class="self-center text-xl font-semibold whitespace-nowrap">{{ tenant('organization') }}</span>
    </div>

    <div class="max-w-lg mt-8 overflow-hidden bg-white border shadow dark:bg-gray-800 sm:rounded-lg">
      <div class="grid grid-cols-1">
        @if ($this->isBlockingAnother)
        <div class="flex p-6 font-medium bg-danger-200/50">
          <div class="flex-shrink-0 w-6 mx-auto text-danger-700 md:w-8 dark:text-danger-500">
            <x-heroicon-o-bell class="origin-top animate-swing" />
          </div>
          <p class="ml-4 text-base text-danger-700">
            You are currently blocking the vehicle with plate number <span class="font-semibold whitespace-nowrap">[ABJ-SCS123]</span>, Kindly repark your vehicle to resolve this issue!
          </p>
        </div>
        @else
        <div class="flex p-6">
          <div class="flex-shrink-0 w-6 mx-auto text-gray-400 md:w-8 dark:text-gray-700">
            <x-heroicon-o-information-circle />
          </div>
          <p class="ml-4 text-sm text-gray-500">
            You can request for another vehicle to repark if they are blocking you from exiting the parking lot.
          </p>
        </div>
        @endif

        <div class="flex items-center justify-around border-t border-gray-300 md:grid md:grid-cols-2 dark:border-gray-700">
          <div class="flex flex-col p-6">
            <h3 class="text-sm font-semibold text-gray-700">
              Expires
              <span x-data="{ tooltip: 'Your access to this dashboard will be disabled after this period.' }" x-tooltip="tooltip" x-on:click="$tooltip(tooltip, { timeout: 5000 })">
                <x-heroicon-o-information-circle class="inline-flex w-4 h-4 text-gray-500" />
              </span>
            </h3>
            <div>
              {{ $access->valid_until->format('g:i A, D') }}
            </div>
          </div>
          <div class="flex flex-col p-6">
            <h3 class="text-sm font-semibold text-gray-700">Parking Lot&nbsp;
              <span class="inline-flex items-center justify-center space-x-1 rtl:space-x-reverse min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-normal text-success-700 bg-success-500/10">{{ $access->parkingLot->status->value }}</span>
            </h3>
            <div>
              {{ $access->parkingLot->name }}
            </div>
          </div>
        </div>

        @if (!$this->isBlockingAnother)
        <div class="flex items-center justify-center p-6 border-t border-gray-300 :border-gray-700">
          <x-filament::button icon="heroicon-o-device-mobile" x-on:click="$dispatch('open-modal', { id: 'request-repark' })">
            {{ __('Request Repark') }}
          </x-filament::button>
        </div>
        @endif
      </div>
    </div>

    <div class="flex items-center justify-between mt-4">
      <div class="text-sm text-center text-gray-500 sm:text-left">
        <div class="flex items-center">
          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-5 h-5 mt-0.5 text-gray-400">
            <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
          </svg>

          <a href="#" class="ml-1 underline">
            Logout
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- trap focus on modal when open -->
  <form wire:submit.prevent="submit">
    <x-filament::modal x-on:click.outside="$dispatch('close-modal', {id: 'request-repark'})" width="md" id="request-repark" heading="Request Repark">
      <x-slot name="subheading">
        Fill in the Plate number of the vehicle that is blocking you and the Driver will be notified to repark their vehicle.
      </x-slot>

      <div class="relative bg-white rounded-lg dark:bg-gray-700">
        {{ $this->form }}
      </div>

      <x-slot name="footer">
        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
          <x-filament::button size='sm' color="secondary" wire:click="cancelRequest" x-on:click="$dispatch('close-modal', {id: 'request-repark'})">
            {{ __('Cancel') }}
          </x-filament::button>

          <x-filament::button size='sm' type="submit">
            {{ __('Request') }}
          </x-filament::button>
        </div>
      </x-slot>
    </x-filament::modal>
  </form>

  <!-- botman contact support -->
  <div id="botmanWidgetRoot" x-data>
    <div style="position: fixed; bottom: 0px; right: 0px; z-index: 2147483647; box-sizing: content-box; overflow: hidden; min-width: 400px; min-height: 120px;">
      <div style="position: relative; cursor: pointer;">
        <button x-tooltip.raw="Contact Support" class="absolute flex justify-center w-[60px] h-[60px] p-2 rounded-full top-10 right-5 bg-primary-800 desktop-closed-message-avatar shadow-md">
          <x-heroicon-o-chat class="w-full h-auto text-white rounded-full" />
        </button>
      </div>
      <div style="display: none; height: 209px;">
      </div>
    </div>
  </div>
</div>
<!-- 
<th scope="col" x-data="{ open: false }" x-on:keydown.escape.stop="open = false" x-on:mousedown.away="open = false" wire:key="header-col-8-HQuhCDoT3e8BGFKEQWoC" class="relative inline-flex items-center px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 whitespace-nowrap dark:bg-gray-800 dark:text-gray-400">
  <span class="uppercase">
    Tags
  </span>

  <button type="button" class="inline-flex justify-center ml-2 text-gray-500" x-on:click="open = !open" aria-haspopup="true" x-bind:aria-expanded="open" aria-expanded="false">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path fillRule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clipRule="evenodd" />
    </svg>
  </button>

  <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" role="menu" aria-orientation="vertical" aria-labelledby="filters-menu" class="absolute left-0 z-50 w-full mt-2 bg-white divide-y divide-gray-100 rounded-md shadow-lg top-6 md:w-56 ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700 dark:text-white dark:divide-gray-600" style="top: 30px; display: none;">
    <div class="py-1" role="none">
      <div class="block px-4 py-2 space-y-1 text-sm text-gray-700" role="menuitem">
        <label for="users1-filter-email_verified_at" class="block text-sm font-medium text-gray-700 ">
          Search Tags
        </label>

        <div class="flex rounded-md shadow-sm">
          <input wire:model="users1.search" type="text" class="block w-full transition duration-150 ease-in-out border-gray-300 rounded-md shadow-sm sm:text-sm sm:leading-5 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Start typing">
        </div>
      </div>
    </div>
  </div>
</th>

<th scope="col" wire:key="header-col-8-HQuhCDoT3e8BGFKEQWoC" class="relative inline-flex items-center px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 whitespace-nowrap dark:bg-gray-800 dark:text-gray-400">
  <span class="uppercase">
    Tags
  </span>

  <button type="button" class="inline-flex justify-center ml-2 text-gray-500" aria-haspopup="true" aria-expanded="false">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path fillRule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clipRule="evenodd" />
    </svg>
  </button>
</th> -->