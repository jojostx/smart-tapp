<div x-data class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
    <div>
        <div class="relative flex flex-col items-center justify-center overflow-hidden">
            <x-logo class="flex-shrink-0 w-auto mb-2 mr-3 text-primary-600 h-9" alt="{{ config('app.name') }} Logo" />
            <span class="self-center text-xl font-semibold whitespace-nowrap">{{ tenant('organization') }}</span>
        </div>

        <div class="max-w-lg mt-8 overflow-hidden bg-white border shadow dark:bg-gray-800 sm:rounded-lg">
            <div class="grid grid-cols-1">
                @if ($this->isBlockingAnother)
                <div class="flex flex-col items-center justify-center p-4 space-y-2">
                    <div class="flex-shrink-0 w-6 mx-auto font-medium text-danger-700 md:w-8 dark:text-danger-500">
                        <x-heroicon-o-bell class="origin-top animate-swing" />
                    </div>
                    <p class="text-base text-center text-danger-700">
                        You are currently blocking one or more vehicles, Kindly repark your vehicle!!!
                    </p>
                    <x-filament::button icon="heroicon-o-fast-forward" @click="$dispatch('open-modal', { id: 'pending-request-repark' })">
                        {{ __('View all blocked vehicles') }}
                    </x-filament::button>
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
                            Access Expires
                            <span x-data="{ tooltip: 'Your access to this dashboard will be disabled after this period.' }" x-tooltip="tooltip" @click="$tooltip(tooltip, { timeout: 5000 })">
                                <x-heroicon-o-information-circle class="inline-flex w-4 h-4 text-gray-500" />
                            </span>
                        </h3>
                        <div>
                            {{ $access->valid_until->format('M, j \a\t g:ia') }}
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

                @unless ($this->isBlockingAnother && $this->hasReparkConfirmations)
                <div class="flex items-center justify-center p-6 border-t border-gray-300 dark:border-gray-700">
                    <!-- trap focus on modal when open -->
                    <form wire:submit.prevent="submit">
                        <x-filament::modal @click.outside="$dispatch('close-modal', {id: 'request-repark'})" width="md" id="request-repark" heading="Request Repark">
                            <x-slot name="subheading">
                                Fill in the Plate number of the vehicle that is blocking you and the Driver will be notified to repark their vehicle.
                            </x-slot>

                            <div class="relative bg-white rounded-lg dark:bg-gray-700">
                                {{ $this->form }}
                            </div>

                            <x-slot name="footer">
                                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                    <x-filament::button size='sm' color="secondary" wire:click="cancelRequest" @click="$dispatch('close-modal', {id: 'request-repark'})">
                                        {{ __('Cancel') }}
                                    </x-filament::button>

                                    <x-filament::button size='sm' type="submit">
                                        {{ __('Request') }}
                                    </x-filament::button>
                                </div>
                            </x-slot>
                        </x-filament::modal>
                    </form>

                    <x-filament::button icon="heroicon-o-device-mobile" @click="$dispatch('open-modal', { id: 'request-repark' })">
                        {{ __('Request Repark') }}
                    </x-filament::button>
                </div>
                @endunless

                @if ($this->hasReparkConfirmations)
                <div class="flex flex-col items-center justify-center p-4 space-y-2 border-t border-gray-300 dark:border-gray-700">
                    <div class="flex-shrink-0 w-6 mx-auto font-medium text-warning-700 md:w-8 dark:text-warning-500">
                        <x-heroicon-o-bell class="origin-top animate-swing" />
                    </div>
                    <p class="text-base text-center text-warning-700">
                        One or more vehicles that are blocking you may have been reparked, Kindly confirm!!!
                    </p>
                    <x-filament::button icon="heroicon-o-fast-forward" @click="$dispatch('open-modal', { id: 'repark-confirmation' })">
                        {{ __('View vehicles') }}
                    </x-filament::button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- trap focus on modal when open -->
    @if ($this->isBlockingAnother)
    <x-filament::modal @click.outside="$dispatch('close-modal', {id: 'pending-request-repark'})" width="md" id="pending-request-repark" heading="Pending Request Repark">
        <x-slot name="subheading">
            You are currently blocking the following vehicles, please repark you vehicle and resolve all the repark requests.
        </x-slot>

        <div class="relative overflow-y-auto bg-white border rounded-lg dark:bg-gray-700 max-h-64">
            <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700">
                @foreach ($this->reparkRequests as $reparkRequest)
                @php
                $vehicle = $reparkRequest->blockeeVehicle;
                @endphp
                <div class="flex items-center justify-between px-2 py-2">
                    <div class="flex flex-col">
                        <dt class="text-gray-500 dark:text-gray-400">Plate Number</dt>
                        <dd class="text-lg font-semibold">{{ $vehicle->plate_number }}</dd>
                        <dd class="text-sm font-medium capitalize">{{ $vehicle->color . ' ' . $vehicle->brand . ' ' . $vehicle->model }}</dd>
                    </div>
                    <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                        <x-filament::button wire:click="requestConfirmation({{ $reparkRequest->id }})" size='sm'>
                            {{ __('Resolve') }}
                        </x-filament::button>
                    </div>
                </div>
                @endforeach
            </dl>
        </div>
    </x-filament::modal>
    @endif

    <!-- trap focus on modal when open -->
    @if ($this->hasReparkConfirmations)
    <x-filament::modal @click.outside="$dispatch('close-modal', {id: 'repark-confirmation'})" width="md" id="repark-confirmation" heading="Repark confirmation">
        <x-slot name="subheading">
            The following vehicles that are blocking your vehicle may have been reparked please confirm.
        </x-slot>

        <div class="relative overflow-y-auto bg-white border rounded-lg dark:bg-gray-700 max-h-64">
            <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700">
                @foreach ($this->reparkConfirmations as $reparkRequest)
                @php
                $vehicle = $reparkRequest->blockerVehicle;
                @endphp
                <div class="flex items-center justify-between px-2 py-2">
                    <div class="flex flex-col">
                        <dt class="text-gray-500 dark:text-gray-400">Plate Number</dt>
                        <dd class="text-lg font-semibold">{{ $vehicle->plate_number }}</dd>
                        <dd class="text-sm font-medium capitalize">{{ $vehicle->color . ' ' . $vehicle->brand . ' ' . $vehicle->model }}</dd>
                    </div>
                    <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                        <x-filament::button wire:click="confirmRepark({{ $reparkRequest->id }})" size='sm'>
                            {{ __('Confirm') }}
                        </x-filament::button>
                    </div>
                </div>
                @endforeach
            </dl>
        </div>
    </x-filament::modal>
    @endif

    <!-- contact support -->
    <div x-data="{
      open: false,
      sendMessage: function() {
          const chatbox = $refs.chatbox;
          const chatboxInput = $refs.chatbox_input;

          if ((message = chatboxInput.value?.trim()) == '') {
            return;
          }
          
          const currentdate = new Date();
          let options = {hour: '2-digit', minute: '2-digit'};
          let time = currentdate.toLocaleTimeString('en-us', options)

          const bubble = `
            <div class='flex flex-row-reverse mb-4'>
              <div class='flex flex-col items-center flex-none ml-4 space-y-1'>
                <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' class='w-6 h-6 rounded-full'><path fill='none' d='M0 0h24v24H0z'/><path d='M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-5-8h2a3 3 0 0 0 6 0h2a5 5 0 0 1-10 0z'/></svg>
                <span class='block text-xs'>You</span>
              </div>
              <div class='relative flex-1 mb-2'>
                <div class='p-2 text-sm text-gray-800 rounded-lg bg-primary-100'>${message}</div>
                <span class='text-xs leading-none text-gray-600'>${time}</span>
              </div>
            </div>`;

          chatboxInput.value = '';
          chatbox.insertAdjacentHTML('beforeend', bubble);
          chatbox.scrollTop = chatbox.scrollHeight;
      }
    }" @keyup.escape.window="open = false">
        <div class="fixed bottom-0 right-0 z-50 overflow-hidden md:mr-4 md:mb-4">
            <div class="relative flex flex-col items-end mb-4 mr-4 space-y-4">
                <div id="chat_modal" x-show="open" @click.outside="open = false" x-trap="open" x-cloak class="flex flex-col bg-white border shadow-xl dark:bg-gray-800 sm:rounded-lg w-80">
                    <nav class="p-2 space-y-2 border-b divide-y shadow">
                        <div class="flex items-center justify-between">
                            <!-- user info -->
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path d="M19.938 8H21a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1.062A8.001 8.001 0 0 1 12 23v-2a6 6 0 0 0 6-6V9A6 6 0 1 0 6 9v7H3a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h1.062a8.001 8.001 0 0 1 15.876 0zM3 10v4h1v-4H3zm17 0v4h1v-4h-1zM7.76 15.785l1.06-1.696A5.972 5.972 0 0 0 12 15a5.972 5.972 0 0 0 3.18-.911l1.06 1.696A7.963 7.963 0 0 1 12 17a7.963 7.963 0 0 1-4.24-1.215z" />
                                </svg>
                                <div class="pl-2">
                                    <p class="font-semibold">{{ $issuer->name ?? 'John Doe' }}</p>
                                    <p class="text-xs text-gray-600">Support Agent</p>
                                </div>
                            </div>
                            <!-- end user info -->

                            <!-- chat box action -->
                            <div>
                                <a class="inline-flex p-2 rounded-full hover:bg-primary-50" href="tel:{{ $issuer->phone_number_e164 }}">
                                    <x-heroicon-o-phone class="w-6 h-6" />
                                </a>

                                <button @click="open = false" class="inline-flex p-2 rounded-full hover:bg-primary-50" type="button">
                                    <x-heroicon-o-x class="w-6 h-6" />
                                </button>
                            </div>
                            <!-- end chat box action -->
                        </div>
                        <div class="pt-2">
                            <span class="flex text-xs text-gray-600">
                                <span>
                                    <svg class="w-4 h-4" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path clip-rule="evenodd" d="M7 13A6 6 0 107 1a6 6 0 000 12z" stroke="#A2A2A2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7 9.999v-3.5a.5.5 0 00-.5-.5h-1m.75-2.5a.25.25 0 110 .5.25.25 0 010-.5M5.5 10h3" stroke="#A2A2A2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>
                                    &nbsp;Typically responds within 30 mins
                                </span>
                            </span>
                        </div>
                    </nav>

                    <!-- <div x-ref="chatbox" class="flex-1 px-4 py-4 space-y-4 overflow-y-auto"> -->
                    <!-- chat message -->
                    <!-- <div class="flex">
                <div class="flex flex-col items-center flex-none mr-4 space-y-1">
                    <img class="w-6 h-6 rounded-full" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />
                </div>
                <div class="relative flex-1">
                    <div class="p-2 text-sm text-white rounded-lg bg-primary-400">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>
                    <span class="text-xs leading-none text-primary-600">2 min ago</span>
                </div>
                </div> -->
                    <!-- end chat message -->

                    <!-- chat message -->
                    <!-- <div class="flex flex-row-reverse">
              <div class="flex flex-col items-center flex-none ml-4 space-y-1">
                <img class="w-6 h-6 rounded-full" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />
                <span class='block text-xs'>You</span>
              </div>
              <div class="relative flex-1">
                <div class="p-2 text-sm text-gray-800 rounded-lg bg-primary-100">Lorem ipsum dolor sit amet, consectetur adipisicing elit.Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>
                <span class="text-xs leading-none text-gray-600">2 min ago</span>
              </div>
            </div> -->
                    <!-- end chat message -->

                    <!-- chat message -->
                    <!-- <div class="flex">
              <div class="flex flex-col items-center flex-none mr-4 space-y-1">
                <img class="w-6 h-6 rounded-full" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />
              </div>
              <div class="relative flex-1">
                <div class="p-2 text-sm text-white rounded-lg bg-primary-400">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>
                <span class="text-xs leading-none text-primary-600">2 min ago</span>
              </div>
            </div> -->
                    <!-- end chat message -->

                    <!-- chat message -->
                    <!-- <div class="flex flex-row-reverse">
              <div class="flex flex-col items-center flex-none ml-4 space-y-1">
                <img class="w-6 h-6 rounded-full" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" />
                <span class='block text-xs'>You</span>
              </div>
              <div class="relative flex-1">
                <div class="p-2 text-sm text-gray-800 rounded-lg bg-primary-100">consectetur adipisicing elit.Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>
                <span class="text-xs leading-none text-gray-600">2 min ago</span>
              </div>
            </div> -->
                    <!-- end chat message -->
                    <!-- </div> -->

                    <!-- <div class="flex items-center p-3 space-x-2 border-t"> -->
                    <!-- <div class="w-full">
              <input x-ref="chatbox_input" @keyup.enter="sendMessage" class="w-full border border-gray-300 rounded-md" type="text" value="" placeholder="Aa" autofocus />
            </div> -->

                    <!-- chat send action -->
                    <!-- <div>
              <button @click="sendMessage" class="inline-flex p-2 rounded-full hover:bg-primary-50" type="button">
                <x-heroicon-o-paper-airplane class="w-6 h-6 rotate-90 rounded-full" />
              </button>
            </div> -->
                    <!-- end chat send action -->
                    <!-- </div> -->
                </div>

                <!-- docked bottom right corner -->
                <button x-tooltip.raw="Contact Support" @click="open = !open" class="relative flex justify-center w-12 h-12 p-2 transform rounded-full shadow-md bg-primary-800">
                    <span id="notification-ping" class="absolute top-0 right-0 flex w-3 h-3">
                        <span class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-danger-400"></span>
                        <span class="relative inline-flex w-3 h-3 rounded-full bg-danger-500"></span>
                    </span>
                    <x-heroicon-o-chat class="w-full h-auto text-white" />
                </button>
                <!-- end docked bottom right corner -->
            </div>
        </div>
    </div>
</div>
