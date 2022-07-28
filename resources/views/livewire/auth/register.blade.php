@section('title', $title)

<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="{{ route('home') }}">
            <x-logo class="w-auto h-16 mx-auto text-primary-600" />
        </a>

        <h2 class="mt-6 text-3xl font-extrabold leading-9 text-center text-gray-900">
            Create a new account
        </h2>

        @if (Route::has('login'))
        <p class="mt-2 text-sm leading-5 text-center text-gray-600 max-w">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium transition duration-150 ease-in-out text-primary-600 hover:text-primary-500 focus:outline-none focus:underline">
                Login
            </a>
        </p>
        @endif
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="px-4 py-8 bg-white shadow sm:rounded-lg sm:px-10">
            <form wire:submit.prevent="register">
                <!-- Organization Name -->
                <div>
                    <x-label for="organization" :value="__('Organization')" :required="true" />

                    <x-input name="organization" wire:model.lazy="organization" id="organization" type="text" required autofocus class="block w-full mt-1 appearance-none" placeholder="(Ex. Google)" :value="old('organization')" />

                    @error('organization')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Domain -->
                <x-domain-input />

                <!-- Name -->
                <div class="mt-4">
                    <x-label for="name" :value="__('Full name')" :required="true" />

                    <x-input name="name" wire:model.lazy="name" id="name" class="block w-full mt-1" placeholder="John Doe" type="text" :value="old('name')" required />

                    @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-label for="email" :value="__('Email address')" :required="true" />

                    <x-input wire:model.lazy="email" id="email" class="block w-full mt-1" placeholder="name@company.com" type="email" name="email" :value="old('email')" required />

                    @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-label for="password" :value="__('Password')" :required="true" />

                    <x-input-password wire:model.lazy="password" id="password" placeholder="••••••••" class="block w-full mt-1" name="password" required autocomplete="new-password" />

                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-label for="password_confirmation" :value="__('Confirm Password')" :required="true" />

                    <x-input-password wire:model.lazy="passwordConfirmation" id="password_confirmation" placeholder="••••••••" class="block w-full mt-1" name="password_confirmation" required autocomplete="new-password" />
                </div>

                <!-- terms and conditions -->
                <div class="mt-4">
                    <label class="flex items-center text-sm font-medium text-gray-700">
                        <x-checkbox wire:model.lazy="terms" name="terms" id="terms" required class="w-4 h-4 mr-2" />

                        {{ __('I agree with the') }}&nbsp;
                        <a href="#" class="font-medium transition duration-150 ease-in-out text-primary-600 hover:text-primary-500 focus:outline-none focus:underline">
                            {{ __('Terms of Service') }}
                        </a>.
                    </label>

                    @error('terms')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-filament::button type="submit" form="submit" class="w-full mt-6">
                    {{ __('Create Account') }}
                </x-filament::button>
            </form>
        </div>
        <x-form-footer />
    </div>
</div>