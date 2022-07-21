@section('title', 'Reset Password')
<div>
  <x-filament::notification-manager />
  @unless ($emailSent && $tenant)
  <div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <a href="{{ route('home') }}">
        <x-logo class="w-auto h-16 mx-auto text-primary-600" />
      </a>

      <h2 class="mt-6 text-3xl font-extrabold leading-9 text-center text-gray-900">
        {{ __('Reset Password') }}
      </h2>

      @unless (blank($this->currentTenant))
        @if (Route::has('filament.auth.login'))
        <p class="mt-2 text-sm leading-5 text-center text-gray-600 max-w">
          <a href="{{ route('filament.auth.login') }}" class="font-medium transition duration-150 ease-in-out text-primary-600 hover:text-primary-500 focus:outline-none focus:underline">
            Login
          </a>
          with your credentials instead?
        </p>
        @endif
      @elseif (Route::has('login'))
      <p class="mt-2 text-sm leading-5 text-center text-gray-600 max-w">
        <a href="{{ route('login') }}" class="font-medium transition duration-150 ease-in-out text-primary-600 hover:text-primary-500 focus:outline-none focus:underline">
          Login
        </a>
        with your credentials instead?
      </p>
      @endunless

    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="px-4 py-8 bg-white shadow sm:rounded-lg sm:px-10">
        <form wire:submit.prevent="sendVerificationNotification">
          <div class="mb-4 text-sm text-gray-600">
            @if (blank($this->currentTenant))
            {{ __("Enter the email address and subdomain associated with your account below and we'll send you an email with instructions to reset your password.") }}
            @else
            {{ __("Enter the email address associated with your account below and we'll send you an email with instructions to reset your password.") }}
            @endif
          </div>

          @if (blank($this->currentTenant))
          <!-- Domain -->
          <x-domain-input />
          @endif

          <!-- Email Address -->
          <div class="mt-4">
            <x-label for="email" :value="__('Email Address')" :required="true" />

            <x-input wire:model.lazy="email" id="email" class="block w-full mt-1" placeholder="name@company.com" type="email" name="email" :value="old('email')" required />

            @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <x-filament::button type="submit" form="submit" class="w-full mt-6">
            {{ __('Send Instructions') }}
          </x-filament::button>
        </form>
      </div>
      <x-form-footer />
    </div>
  </div>
  @else
  <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
    <div class="flex flex-col items-center justify-center px-6 py-12 text-center bg-white shadow sm:rounded-lg">
      <div class="p-4 rounded-lg bg-primary-50">
        <x-heroicon-s-mail-open class="w-12 h-12 text-primary-600" />
      </div>

      <div class="mt-8">
        <p class="text-3xl font-semibold leading-5">
          Check Your Mail
        </p>
      </div>

      <div class="mt-4">
        <p class="max-w-xs font-medium leading-5 text-gray-700">
          The Password recovery instructions have been sent to your email.
        </p>
      </div>

      <div class="mt-8">
        <p class="text-sm leading-5 text-gray-600">
          Did not see the email in your inbox? Check your SPAM folder,
          or <button type="button" class="font-medium transition duration-150 ease-in-out text-primary-600 hover:text-primary-500 focus:outline-none focus:underline"> try another email address</button>
        </p>
      </div>
    </div>
    <x-form-footer />
  </div>
  @endunless
</div>