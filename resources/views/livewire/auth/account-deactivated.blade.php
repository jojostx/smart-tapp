@section('title', $title)

<div>
  <div class="sm:mx-auto sm:w-full sm:max-w-md">
    <a href="{{ route('home') }}">
      <x-logo class="w-auto h-16 mx-auto text-primary-600" />
    </a>

    <div class="mt-4">
        <h2 class="text-2xl font-bold tracking-tight text-center">
            {{ __('Account Deactivated') }}
        </h2>
    </div>

    <div class="mt-4">
      <p class="text-center">
          {{ __('This account is Deactivated. Please contact an administrator.') }}
      </p>
    </div>


    <div class="mt-4 text-center">
        <a class="text-primary-600 hover:text-primary-700" href="{{ route('filament.auth.login') }}">
            {{ __('Back to login') }}
        </a>
    </div>
</div>