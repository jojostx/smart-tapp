@section('title', $title)

<div>
  <x-filament::notification-manager />
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <a href="{{ route('home') }}">
      <x-logo class="w-auto h-16 mx-auto text-primary-600" />
    </a>

    <h2 class="mt-6 text-3xl font-extrabold leading-9 text-center text-gray-900">
      {{ __('Create New Password') }}
    </h2>

    @unless (blank(tenant()))
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
      <form wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button type="submit" form="submit" class="w-full mt-6">
          {{ __('Reset Password') }}
        </x-filament::button>
      </form>
    </div>
    <x-form-footer />
  </div>
</div>