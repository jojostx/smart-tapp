@extends('layouts.app')

@section('content')

<section>
  <form method="POST" action="{{ route('filament.plans.checkout.create') }}" id="paymentForm" x-data="{ open : false }" class="grid h-screen grid-cols-1 md:grid-cols-2 auto-rows-min md:auto-rows-auto">
    @csrf

    <div class="grid order-last grid-cols-1 bg-white md:order-first lg:grid-cols-12">
      <div class="px-6 py-8 md:col-span-9 lg:col-span-10 xl:col-span-9 lg:col-end-13 xl:col-end-13 md:px-10 lg:px-16 md:py-32">
        <h1 class="text-lg font-bold md:text-xl text-primary-900 leading-extra-tight">Checkout</h1>
        <div class="mt-6">
          <!-- Domain -->
          <div>
            <x-label for="domain" :value="__('Domain')" />

            <x-input name="domain" id="domain" type="text" class="block w-full mt-1 appearance-none" placeholder="e.g. Acme Ltd" :value="old('domain') ?? str($tenant?->domain)->before('.')" disabled />
          </div>

          <div class="mt-4">
            <button @click.prevent="open = !open" class="flex items-center justify-center text-xs text-primary-600">
              Add invoice details (optional) &nbsp;
              <x-heroicon-o-chevron-down ::class="{ 'rotate-180' : open }" class="w-4 h-4 transition-all duration-200 " />
            </button>
            <div x-show="open" x-cloak class="mt-4 space-y-4">
              <!-- Organization Name -->
              <div>
                <x-label for="organization" :value="__('Organization name')" />

                <x-input name="organization" id="organization" type="text" class="block w-full mt-1 appearance-none" placeholder="e.g. Acme Ltd" :value="old('organization')" />

                @error('organization')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- Name -->
              <div>
                <x-label for="name" :value="__('Full name')" />

                <x-input name="name" id="name" class="block w-full mt-1" placeholder="e.g. John Doe" type="text" :value="old('name')" />

                @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- Phone Number -->
              <div>
                <x-label for="phone_number" :value="__('Phone number')" />

                <x-input name="phone_number" id="phone_number" class="block w-full mt-1" placeholder="e.g. +2348034081380" type="tel" :value="old('phone_number')" />

                @error('phone_number')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- Full address -->
              <div>
                <x-label for="full_address" :value="__('Full address')" />

                <x-input name="full_address" id="full_address" class="block w-full mt-1" placeholder="e.g. 123 Code Road, 12345" type="text" :value="old('full_address')" />

                @error('full_address')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>
          </div>

          <x-filament::button type="submit" form="submit" class="w-full mt-6">
            {{ __('Proceed to Payment') }}
          </x-filament::button>

          <x-form-footer />
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 pt-6 bg-gray-900 lg:grid-cols-12 md:pt-0">
      <div class="px-6 py-8 md:col-span-9 lg:col-span-10 xl:col-span-9 md:px-10 lg:px-16 pt-14 md:py-32">
        <h1 class="mb-6 text-lg font-bold text-white md:text-xl md:w-8/12 leading-extra-tight md:mb-10"> Chosen plan </h1>

        <ul class="w-full font-medium text-gray-900 bg-white border border-gray-200 divide-y rounded-lg">
          @foreach ($plans as $plan)
          <li class="flex items-center px-3">
            <input id="list-radio-{{ $plan->name }}" name="plan" value="{{ $plan->slug }}" @checked(old('plan')==$plan->slug || $selected_plan?->slug == $plan->slug) type="radio" class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 focus:ring-2">
            <label for="list-radio-{{ $plan->name }}" class="flex items-center justify-between w-full py-4 ml-2 text-gray-900">
              <span class="capitalize">
                {{ $plan->name }}
              </span>
              <span class="text-base leading-none text-gray-600">
                {{ currency($plan->currency)->getSymbol() }}{{ number_format(money($plan->price, $plan->currency)->getValue()) }}
                <span class="ml-1 text-gray-500">/ <span>year</span>
                </span>
            </label>
          </li>
          @endforeach
        </ul>

        <div class="mt-6 text-sm text-gray-400">
          <p>Nice choice. You can swap your plan any time during your subscription if you change your mind.</p>
          <p class="hidden mt-12 md:flex">
            <x-heroicon-o-lock-closed class="w-8 h-8 mr-2" />
            This is a secure checkout, your payment details don't touch our servers.
          </p>
        </div>
      </div>
    </div>
  </form>
</section>

@endsection