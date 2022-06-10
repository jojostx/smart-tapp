@section('title', 'Create a new account')

<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="{{ route('home') }}">
            <x-logo class="w-auto h-16 mx-auto text-indigo-600" />
        </a>

        <h2 class="mt-6 text-3xl font-extrabold leading-9 text-center text-gray-900">
            Create a new account
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="px-4 py-8 bg-white shadow sm:rounded-lg sm:px-10">
            <form wire:submit.prevent="register">
                <!-- Organization Name -->
                <div>
                    <x-label for="organization" :value="__('Organization')" />

                    <x-input name="organization" wire:model.lazy="organization" id="organization" type="text" required autofocus  class="block w-full mt-1 appearance-none" placeholder="(Ex. Google)" :value="old('organization')"/>
                    
                    @error('organization')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Domain -->
                <div class="mt-4">
                    <div class="flex items-baseline">
                        <x-label for="domain" :value="__('Domain')" />
                        
                        <div class="ml-1 text-xs cursor-pointer" x-data="{ tooltip: 'This is crazy!' }">
                            <button x-tooltip="tooltip">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <x-input-group-end wire:model.lazy="domain" x-data="" :x-mask="'a' . str_repeat('*', config('tenancy.subdomain_maxlength') - 1)" id="domain" placeholder="(Ex. acme)" name="domain" suffix="{{ '.' . config('tenancy.central_domains')[0] }}" :maxlength="config('tenancy.subdomain_maxlength')" :value="old('domain')" required />
                
                    @if ($errors->has('domain') || $errors->has('fqsd'))
                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('domain') }}</p>
                    @endif
                </div>

                 <!-- Name -->
                <div class="mt-4">
                    <x-label for="name" :value="__('Name')" />

                    <x-input name="name" wire:model.lazy="name" id="name" class="block w-full mt-1" placeholder="John Doe" type="text" :value="old('name')" required />

                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-label for="email" :value="__('Email')" />

                    <x-input wire:model.lazy="email" id="email" class="block w-full mt-1" placeholder="name@company.com" type="email" name="email" :value="old('email')" required />

                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-label for="password" :value="__('Password')" />

                    <x-input-password wire:model.lazy="password" id="password" placeholder="••••••••" class="block w-full mt-1" name="password" required autocomplete="new-password" />

                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-label for="password_confirmation" :value="__('Confirm Password')" />

                    <x-input-password wire:model.lazy="passwordConfirmation" id="password_confirmation" placeholder="••••••••" class="block w-full mt-1" name="password_confirmation" required autocomplete="new-password" />
                </div>

                <!-- terms and conditions -->
                <div class="mt-4">
                    <label class="flex items-center text-sm font-medium text-gray-700">
                        <x-checkbox wire:model.lazy="terms" name="terms" id="terms" required class="w-4 h-4 mr-2"/>
                        
                        {{ __('I agree with the') }}&nbsp;<a target="_blank" href="{{ $terms_route ?? '#' }}" class="text-blue-600 hover:underline">{{ __('Terms and Conditions') }}</a>.
                    </label>

                    @error('terms')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <span class="block w-full rounded-md shadow-sm">
                        <button type="submit" class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700">
                            Register
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
