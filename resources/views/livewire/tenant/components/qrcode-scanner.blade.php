<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
    <div class="relative flex flex-col items-center justify-center overflow-hidden">
        <x-logo class="flex-shrink-0 w-auto mb-2 mr-3 text-primary-600 h-9" alt="{{ config('app.name') }} Logo" />
        <span class="self-center text-xl font-semibold whitespace-nowrap">{{ tenant('organization') }}</span>
    </div>
    <form wire:submit.prevent="authenticate">
        <input type="hidden" wire:model="parking_lot_id" id="parking_lot_id" x-ref="" required>
    </form>
    <div wire:ignore x-data="qrcodeScanner" class="max-w-md mt-8 overflow-hidden bg-white border shadow dark:bg-gray-800 sm:rounded-lg">
        <div class="grid grid-cols-1">
            <div class="p-6 text-center">
                <div class="w-6 mx-auto text-gray-400 dark:text-gray-700">
                    <x-heroicon-o-information-circle />
                </div>
                <p class="text-sm text-gray-500">
                    Scan the QR code at the Parking Lot designated to you to gain access.
                </p>
            </div>
            <div x-bind:class="{ 'md:p-4': isScanning, 'md:p-6': !isScanning }" class="relative flex flex-col items-center justify-center px-2 py-4 border-t border-gray-300 h-80 max-h-80 dark:border-gray-700 md:p-6">
                <div x-show="!isScanning" class="absolute z-20 max-w-full text-gray-500 bg-white max-h-80">
                    <x-heroicon-o-qrcode class="w-2/3 m-auto border-8 rounded-lg" />
                </div>
                <video id="qr-video" x-ref="qr_scanner" class="w-full my-0.5 rounded-lg md:my-0"></video>
            </div>

            <div class="px-6 pt-2 pb-6 m-auto">
                <x-filament::button x-on:click="startScanning()" x-show="!isScanning" icon="heroicon-o-camera" color="primary" class="w-full">
                    Scan Qr Code
                </x-filament::button>
                <x-filament::button x-on:click="stopScanning()" x-show="isScanning" x-cloak icon="heroicon-o-ban" color="danger" class="w-full">
                    Stop Scanning
                </x-filament::button>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center mt-4">
        <x-form-footer />
    </div>

    @pushOnce('scripts:head-start')
    <script src="{{ url(mix('js/qr-scanner.js')) }}" defer></script>
    @endPushOnce
</div>