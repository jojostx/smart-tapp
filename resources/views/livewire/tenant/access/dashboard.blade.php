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
                    <x-filament::button icon="heroicon-o-fast-forward" @click="$dispatch('open-modal', { id: 'pending-repark-request' })">
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

                @if (!$this->isBlockingAnother && !$this->hasReparkConfirmations)
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
                @endif

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
    <x-filament::modal @click.outside="$dispatch('close-modal', {id: 'pending-repark-request'})" width="md" id="pending-repark-request" heading="Pending Repark Request">
        <x-slot name="subheading">
            You are currently blocking the following vehicles, please repark your vehicle and resolve all the repark requests.
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
    <livewire:components.chat :access="$access" />
</div>
