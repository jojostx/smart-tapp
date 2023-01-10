@section('title', $title)

<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="{{ route('home') }}">
            <x-logo class="w-auto h-16 mx-auto text-primary-600" />
        </a>

        <h2 class="mt-6 text-3xl font-extrabold leading-9 text-center text-gray-900">
            Verify Email Address
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="px-4 py-8 bg-white shadow sm:rounded-lg sm:px-10">
            @if ($emailSent)
            <div class="p-4 rounded-md bg-green-50">
                <div>
                    <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>

                <div class="mt-2">
                    <p class="text-sm font-medium leading-5 text-green-800">
                        A One Time Passcode has been sent to <span class="font-semibold">{{ $email }}</span>
                    </p>
                </div>

                <div class="mt-2">
                    <p class="text-sm font-medium leading-5 text-green-800">
                        Please enter the OTP below to verify your Email Address. if you cannot see the email from
                        <span class="font-semibold">{{ config('app.name') }}</span> in your inbox, make sure to check your SPAM folder.
                    </p>
                </div>
            </div>
            <form wire:submit.prevent="verifyEmail" class="mt-4">
                <div>
                    <x-label for="otp" :value="__('OTP code')" :required="true" />

                    <x-input wire:model.lazy="otp" id="otp" name="otp" type="text" required autofocus class="block w-full mt-1 appearance-none" />

                    @error('otp')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mt-4">
                    <x-button type="submit" class="flex justify-center text-sm font-medium capitalize">Verify OTP</x-button>

                    <button type="button" wire:click.prevent="sendVerificationNotification" class="flex justify-center px-4 py-2 text-sm font-medium underline border-0 hover:text-primary-500 focus:text-primary-600">
                        Resend OTP
                    </button>
                </div>
            </form>
            @else
            <form wire:submit.prevent="sendVerificationNotification">
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Just let us know your Email Address and we will email you an OTP (one-time passcode).') }}
                </div>
                <div>
                    <x-label for="email" :value="__('Email Address')" :required="true" />

                    <x-input wire:model.lazy="email" id="email" class="block w-full mt-1" placeholder="name@company.com" type="email" name="email" :value="old('email')" required />

                    @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-filament::button type="submit" wire:target="verifyEmail" form="submit" class="w-full mt-6">
                    {{ __('Send Verification Mail') }}
                </x-filament::button>
            </form>
            @endif
        </div>
        <x-form-footer />
    </div>

    @if ($isCreatingAccount)
    <div x-data="{ show: true }" x-show="show" wire:poll.8s="redirectIfAccountHasBeenPrepared" class="fixed inset-0 z-10 flex flex-col items-center justify-center bg-gray-50">
        <div x-trap.inert.noscroll="show" class="p-12 text-center bg-white border shadow-md rounded-xl">
            <div>
                <svg class="w-20 h-20 m-auto" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                    <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-400" stroke-width="3">
                        <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="0s"></animate>
                        <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="0s"></animate>
                    </circle>
                    <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-800" stroke-width="3">
                        <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="-0.5s"></animate>
                        <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="-0.5s"></animate>
                    </circle>
                </svg>
            </div>
            <div class="mt-6">
                <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                    We're building your Account.
                </h2>

                <p class="mt-12 text-lg font-semibold text-gray-600">
                    Please wait while our 🤖 robots build your account.
                </p>

                <p class="mt-2 text-lg font-semibold text-gray-600">
                    It shouldn't take more than a minute
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
