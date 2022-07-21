@section('title', 'Sign in to your account')

<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="{{ route('home') }}">
            <x-logo class="w-auto h-16 mx-auto text-primary-600" />
        </a>

        <h2 class="mt-6 text-3xl font-extrabold leading-9 text-center text-gray-900">
            Sign in to your account
        </h2>
        @unless (tenant())
            @if (Route::has('register'))
            <p class="mt-2 text-sm leading-5 text-center text-gray-600 max-w">
                Or
                <a href="{{ route('register') }}" class="font-medium transition duration-150 ease-in-out text-primary-600 hover:text-primary-500 focus:outline-none focus:underline">
                    create a new account
                </a>
            </p>
            @endif
        @endunless
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="px-4 py-8 bg-white shadow sm:rounded-lg sm:px-10">
            <form wire:submit.prevent="authenticate">
                @if (blank($this->currentTenant))
                <!-- Domain -->
                <x-domain-input />
                @endif

                <!-- Email Address -->
                <div class="mt-4">
                    <x-label for="email" :value="__('Email')" :required="true" />

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

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center">
                        <x-checkbox wire:model.lazy="remember" id="remember" name="remember" class="w-4 h-4 mr-2" />

                        <label for="remember" class="block ml-1 text-sm leading-5 text-gray-900">
                            Remember
                        </label>
                    </div>

                    <div class="text-sm leading-5">
                        <a href="{{ route('password.request') }}" class="font-medium transition duration-150 ease-in-out text-primary-600 hover:text-primary-500 focus:outline-none focus:underline">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <x-filament::button type="submit" form="submit" class="w-full mt-6">
                    {{ __('Sign in') }}
                </x-filament::button>
            </form>
        </div>

        <x-form-footer />
    </div>
</div>