<div x-data="qrcodeScanner" class="max-w-6xl mx-auto sm:px-6 lg:px-8">
    <div class="relative flex flex-col items-center justify-center overflow-hidden">
        <x-logo class="flex-shrink-0 w-auto mb-2 mr-3 text-primary-600 h-9" alt="{{ config('app.name') }} Logo" />
        <span class="self-center text-xl font-semibold whitespace-nowrap">{{ tenant('organization') }}</span>
    </div>

    <form wire:submit.prevent="authenticate">
        <input type="hidden" wire:model="parking_lot_id" id="parking_lot_id" x-ref="" required>
    </form>
    <div wire:ignore class="max-w-md mt-8 overflow-hidden bg-white border shadow dark:bg-gray-800 sm:rounded-lg">
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
                <div x-show="isScanning" x-cloak class="flex items-center justify-center w-full">
                    <x-filament::button x-on:click="stopScanning()" icon="heroicon-o-ban" color="danger" class="mr-2">
                        Stop Scanning
                    </x-filament::button>
                    <button x-on:click="toggleFlash()" x-show="canUseFlash" x-cloak title="toggle flash" role="switch" aria-checked="false" class="relative inline-flex h-6 transition-colors duration-200 ease-in-out bg-gray-300 border-2 border-transparent rounded-full cursor-pointer shrink-0 w-11 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70 disabled:cursor-not-allowed disabled:pointer-events-none" type="button">
                        <span x-bind:class="{ 'translate-x-5 rtl:-translate-x-5': isFlashOn, 'translate-x-0': ! isFlashOn }" class="relative inline-block w-5 h-5 transition duration-200 ease-in-out transform bg-white rounded-full shadow pointer-events-none ring-0">
                            <span x-bind:class="{ 'opacity-0 ease-out duration-100': isFlashOn, 'opacity-100 ease-in duration-200': ! isFlashOn }" class="absolute inset-0 flex items-center justify-center w-full h-full transition-opacity" aria-hidden="true">
                                <x-heroicon-o-moon class="w-3 h-3 text-gray-400" />
                            </span>

                            <span x-bind:class="{ 'opacity-100 ease-in duration-200': isFlashOn, 'opacity-0 ease-out duration-100': ! isFlashOn }" class="absolute inset-0 flex items-center justify-center w-full h-full transition-opacity" aria-hidden="true">
                                <x-heroicon-o-sun x-cloak class="w-3 h-3 text-warning-600" />
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center mt-4">
        <x-form-footer />
    </div>

    <div wire:ignore x-show="isProcessing" x-trap.inert.noscroll="isProcessing" x-cloak class="fixed inset-0 z-40 flex flex-col items-center justify-center bg-gray-50">
        <div class="p-12 text-center bg-white border shadow-md rounded-xl">
            <a href="#" class="opacity-0"></a>
            <div>
                <svg class="w-20 h-20 m-auto" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                    <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-400" stroke-width="6">
                        <animate attributeName="r" repeatCount="indefinite" dur="2s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="0s"></animate>
                        <animate attributeName="opacity" repeatCount="indefinite" dur="2s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="0s"></animate>
                    </circle>
                    <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-800" stroke-width="6">
                        <animate attributeName="r" repeatCount="indefinite" dur="2s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="-1s"></animate>
                        <animate attributeName="opacity" repeatCount="indefinite" dur="2s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="-1s"></animate>
                    </circle>
                </svg>
            </div>
            <div class="mt-4">
                <p class="text-lg font-semibold">...Processing</p>
                <p class="mt-2 text-sm">You will be redirected soon. If you are not redirected after 30 seconds you can retry or <a href="tel:+2348034081360" class="underline text-primary-600 hover:text-primary-900">contact support</a></p>
                <x-filament::button x-on:click="startScanning()" icon="heroicon-o-refresh" color="primary" class="mt-4">
                    Retry Scan
                </x-filament::button>
            </div>
        </div>
    </div>

    @pushOnce('scripts:head-start')
    <script src="{{ url(mix('js/qr-scanner.js')) }}" defer></script>
    @endPushOnce
</div>