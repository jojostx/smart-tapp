<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
  <div>
    <div class="relative flex flex-col items-center justify-center overflow-hidden">
      <x-logo class="flex-shrink-0 w-auto mb-2 mr-3 text-primary-600 h-9" alt="{{ config('app.name') }} Logo" />
      <span class="self-center text-xl font-semibold whitespace-nowrap">{{ tenant('organization') }}</span>
    </div>

    <div class="max-w-lg mt-8 overflow-hidden bg-white border shadow dark:bg-gray-800 sm:rounded-lg">
      <div class="grid grid-cols-1">
        <div class="p-6 text-center">
          <div class="w-6 mx-auto text-gray-400 dark:text-gray-700">
            <x-heroicon-o-information-circle />
          </div>
          <p class="text-sm text-gray-500">
            You can request for another vehicle to repark if they are blocking you from exiting the parking lot.
          </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 border-t border-gray-300 dark:border-gray-700">
          <div class="flex items-center p-6">
            <x-heroicon-o-device-mobile class="w-8 h-8 text-gray-500" />
            <div class="ml-4">
                {{ $access->status->value }}
            </div>
          </div>
          <div class="flex items-center p-6">
            <x-heroicon-o-device-mobile class="w-8 h-8 text-gray-500" />
            <div class="ml-4">
                {{ $access->valid_until->diffForHumans() }}
            </div>
          </div>
          <div class="flex items-center p-6">
            <x-heroicon-o-device-mobile class="w-8 h-8 text-gray-500" />
            <div class="ml-4">
                {{ $access->parkingLot->name }}
            </div>
          </div>
          <div class="flex items-center p-6">
            <x-heroicon-o-device-mobile class="w-8 h-8 text-gray-500" />
            <div class="ml-4">
                {{ $access->parkingLot->status->value }}
            </div>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 border-t border-gray-300 dark:border-gray-700">
          <div class="flex items-center p-6">
            <x-heroicon-o-device-mobile class="w-8 h-8 text-gray-500" />
            <div class="ml-4 text-lg font-semibold leading-7">
              <a href="#" class="text-gray-900 underline dark:text-white">Request Repark</a>
            </div>
          </div>

          <div class="flex items-center p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
            <x-heroicon-o-paper-airplane class="w-8 h-8 text-gray-500" />
            <div class="ml-4 text-lg font-semibold leading-7">
              <a href="#" class="text-gray-900 underline dark:text-white">Contact Support</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex items-center mt-4 justify-between">
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

      <div class="ml-4 text-sm text-center text-gray-500 sm:text-right sm:ml-0">
        Expires in: {{ $access->valid_until->diffForHumans() }}
      </div>
    </div>
  </div>
</div>