<x-guest-layout>
    <x-auth-card>
        <div class="flex items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-900">
                Create Your Account
            </h1>
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Organization Name -->
            <div>
                <x-label for="organization" :value="__('Organization')" />

                <x-input id="organization" class="block w-full mt-1" placeholder="(Ex. Google)" type="text" name="organization" :value="old('organization')" required autofocus />
            </div>

            <!-- Domain -->
            <div class="mt-4">
                <div class="flex items-baseline">
                    <x-label for="domain" :value="__('Domain')" />
                    <div class="ml-1 text-xs cursor-pointer" x-data="{ tooltip: 'This is crazy!' }">
                        <button x-tooltip="tooltip">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>

                <x-input-group-end id="domain" placeholder="(Ex. acme)" suffix="{{ '.' . config('tenancy.central_domains')[0] }}" :name="'domain'" :value="old('domain')" :maxlength="config('tenancy.subdomain_maxlength')" required />
            </div>


            <!-- Name -->
            <div class="mt-4">
                <x-label for="name" :value="__('Name')" />

                <x-input id="name" class="block w-full mt-1" placeholder="John Doe" type="text" name="name" :value="old('name')" required />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block w-full mt-1" placeholder="name@company.com" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input-password id="password" placeholder="••••••••" class="block w-full mt-1" name="password" required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-input id="password_confirmation" placeholder="••••••••" class="block w-full mt-1" type="password" name="password_confirmation" required />
            </div>

            <!-- terms and conditions -->
            <div class="mt-4">
                <label class="flex items-center text-sm font-medium text-gray-700">
                    <input name="terms" id="terms" type="checkbox" required class="w-4 h-4 mr-2 text-blue-600 bg-gray-100 border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                    {{ __('I agree with the') }}&nbsp;<a target="_blank" href="{{ $terms_route ?? '#' }}" class="text-blue-600 hover:underline">{{ __('Terms and Conditions') }}</a>.
                </label>
            </div>

            <div class="flex items-center mt-4">
                <x-button class="flex items-center justify-center w-full">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>