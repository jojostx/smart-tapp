<div x-data="{ navOpen: false, tooltip: { content: 'Notifications', theme: Alpine.store('theme') === 'light' ? 'dark' : 'light', placement: 'bottom', } }" @click.outside="navOpen = false" class="relative ml-4">
  <button x-tooltip.html="tooltip" @click="$refs.panel.toggle; navOpen = ! navOpen" :class="navOpen ? 'text-primary-500 bg-primary-500/10' : ''" title="Notifications Trigger" type="button" class="relative flex items-center justify-center w-10 h-10 text-sm font-medium text-center text-gray-500 rounded-md hover:text-gray-900 dark:hover:text-white dark:text-gray-400 filament-icon-button hover:bg-gray-500/5 focus:outline-none focus:text-primary-500 focus:bg-primary-500/10" aria-expanded="false" aria-controls="panel-1uT4AAWg">
    <span class="sr-only">
      Notifications
    </span>

    <x-heroicon-s-bell class="w-6 h-6 filament-icon-button-icon" />

    @if ($this->hasUnreadNotifications)
    <span class="absolute block w-3 h-3 border-2 border-white rounded-full bg-danger-500 top-1 right-2 dark:border-gray-900"></span>
    @endif
  </button>

  <!-- Dropdown menu -->
  <div x-ref="panel" x-transition="" x-float.placement.bottom-end.offset.shift="{ offset: 10 }" style="position: fixed;" id="panel-1uT4AAWg" class="absolute z-20 hidden max-w-sm overflow-hidden bg-white rounded-md shadow-xl filament-action-group-dropdown ring-1 ring-gray-900/10 dark:bg-gray-700" aria-modal="true" role="dialog">
    <div class="block px-4 py-2 font-medium text-center text-gray-700 bg-gray-50 dark:bg-gray-800 dark:text-white">
      Notifications
    </div>

    @forelse ($unreadNotifications as $notification)
    <div class="px-3 pb-3 overflow-y-auto text-sm text-gray-700 h-72 dark:text-gray-200">
      <x-notification-card class="bg-white border-b border-gray-200 rounded dark:border-gray-700" :close_action="false">
        <x-slot:icon>
          <div tabindex="0" aria-label="group icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border border-gray-200 rounded-full focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg2.svg" alt="icon" />
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight focus:outline-none"><span class="text-indigo-700">James Doe</span> favourited an <span class="text-indigo-700">item</span></p>
          <p tabindex="0" class="pt-1 text-xs leading-3 text-gray-500 focus:outline-none">2 hours ago</p>
        </x-slot>
      </x-notification-card>

      <x-notification-card class="bg-white border-b border-gray-200 rounded dark:border-gray-700" :close_action="false">
        <x-slot:icon>
          <div tabindex="0" aria-label="group icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border border-gray-200 rounded-full focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg5.svg" alt="icon" />
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight focus:outline-none"><span class="text-indigo-700">Sarah</span> posted in the thread: <span class="text-indigo-700">Update gone wrong</span></p>
          <p tabindex="0" class="pt-1 text-xs leading-3 text-gray-500 focus:outline-none">2 hours ago</p>
        </x-slot>
      </x-notification-card>

      <x-notification-card class="bg-white border-b border-gray-200 rounded dark:border-gray-700" :close_action="false">
        <x-slot:icon>
          <div tabindex="0" aria-label="group icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border border-gray-200 rounded-full focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg7.svg" alt="icon" />
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight focus:outline-none">Shipment delayed for order<span class="text-indigo-700"> #25551</span></p>
          <p tabindex="0" class="pt-1 text-xs leading-3 text-gray-500 focus:outline-none">2 hours ago</p>
        </x-slot>
      </x-notification-card>

      <x-notification-card class="flex items-center border-b border-gray-200 rounded bg-danger-100 dark:border-gray-700" :close_action="false">
        <x-slot:icon>
          <div tabindex="0" aria-label="storage icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border rounded-full border-danger-200 focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg6.svg" alt="icon" />
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight text-danger-700 focus:outline-none">Low on storage: 2.5/32gb remaining</p>
        </x-slot>

        <x-slot:close_action>
          <p tabindex="0" class="text-xs leading-3 text-right underline cursor-pointer text-danger-700 focus:outline-none">Manage</p>
        </x-slot>
      </x-notification-card>

      <x-notification-card class="bg-white rounded shadow">
        <x-slot:icon>
          <div tabindex="0" aria-label="group icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border border-gray-200 rounded-full focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg3.svg" alt="icon">
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight focus:outline-none"><span class="text-primary-700">Sash</span> added you to the group: <span class="text-primary-700">UX Designers</span></p>
          <p tabindex="0" class="pt-1 mt-1 text-xs leading-3 text-gray-500 focus:outline-none">2 hours ago</p>
        </x-slot>
      </x-notification-card>

      <h2 tabindex="0" class="pt-8 pb-2 text-sm leading-normal text-gray-600 border-b border-gray-300 focus:outline-none">YESTERDAY</h2>
      
      <x-notification-card class="bg-white border-b border-gray-200 rounded dark:border-gray-700" :close_action="false">
        <x-slot:icon>
          <div tabindex="0" aria-label="group icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border border-gray-200 rounded-full focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg2.svg" alt="icon" />
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight focus:outline-none"><span class="text-indigo-700">James Doe</span> favourited an <span class="text-indigo-700">item</span></p>
          <p tabindex="0" class="pt-1 text-xs leading-3 text-gray-500 focus:outline-none">2 hours ago</p>
        </x-slot>
      </x-notification-card>

      <x-notification-card class="bg-white border-b border-gray-200 rounded dark:border-gray-700" :close_action="false">
        <x-slot:icon>
          <div tabindex="0" aria-label="group icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border border-gray-200 rounded-full focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg5.svg" alt="icon" />
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight focus:outline-none"><span class="text-indigo-700">Sarah</span> posted in the thread: <span class="text-indigo-700">Update gone wrong</span></p>
          <p tabindex="0" class="pt-1 text-xs leading-3 text-gray-500 focus:outline-none">2 hours ago</p>
        </x-slot>
      </x-notification-card>

      <x-notification-card class="bg-white border-b border-gray-200 rounded dark:border-gray-700" :close_action="false">
        <x-slot:icon>
          <div tabindex="0" aria-label="group icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border border-gray-200 rounded-full focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg7.svg" alt="icon" />
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight focus:outline-none">Shipment delayed for order<span class="text-indigo-700"> #25551</span></p>
          <p tabindex="0" class="pt-1 text-xs leading-3 text-gray-500 focus:outline-none">2 hours ago</p>
        </x-slot>
      </x-notification-card>

      <x-notification-card class="flex items-center border-b border-gray-200 rounded bg-success-100 dark:border-gray-700" :close_action="false">
        <x-slot:icon>
          <div tabindex="0" aria-label="success icon" role="img" class="flex items-center justify-center flex-shrink-0 w-8 h-8 border rounded-full border-success-200 focus:outline-none">
            <img src="https://tuk-cdn.s3.amazonaws.com/can-uploader/notification_1-svg11.svg" alt="icon" />
          </div>
        </x-slot>
        <x-slot:body>
          <p tabindex="0" class="text-sm leading-tight text-success-700 focus:outline-none">Design sprint completed</p>
        </x-slot>

        <x-slot:close_action>
          <p tabindex="0" class="text-xs leading-3 underline cursor-pointer text-success-700 focus:outline-none focus:text-indigo-600">View</p>
        </x-slot>
      </x-notification-card>

      <div class="flex items-center justiyf-between">
        <hr class="w-full">
        <p tabindex="0" class="flex flex-shrink-0 px-3 py-16 text-sm leading-normal text-gray-500 focus:outline-none">Thats it for now :)</p>
        <hr class="w-full">
      </div>
    </div>
    @empty
    <div class="flex flex-col items-center justify-center h-64 px-3 pb-3 text-center">
      <p class="flex flex-shrink-0 px-3 text-xl font-semibold leading-normal text-gray-500 focus:outline-none">
        No Notifications
      </p>
      <p class="flex flex-shrink-0 px-3 text-sm leading-normal text-gray-500 focus:outline-none">
        You'll be notified when something arrives.
      </p>
    </div>
    @endforelse

    
    @if ($this->hasUnreadNotifications)
    <a href="#" class="flex items-center justify-center p-3 text-sm font-medium text-gray-900 border-t border-gray-200 bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
      <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
      </svg>
      View all
    </a>
    @endif
  </div>
</div>