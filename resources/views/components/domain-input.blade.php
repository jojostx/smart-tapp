@props([
  'disabled' => false,
  'id' => 'domain',
  'name' => 'domain',
  'label' => 'Domain',
  'tooltip' => 'This is the subdomain for your account',
  'suffix' => '.' . config('tenancy.central_domains.main'),
  'maxlength' => config('tenancy.subdomain_maxlength'),
  'required' => true,
  'placeholder' => 'e.g. Acme',
  'mask' => 'a' . str_repeat('*', config('tenancy.subdomain_maxlength') - 1),
  'wire_lazy' => null,
])

@php
  $has_error = $errors->has('fqsd') || $errors->has('domain');
@endphp
<div class="mt-4">
  <div class="flex items-baseline">
    <x-label :for="$id" :value="__($label)" />
    <div class="ml-1 text-xs cursor-pointer" x-data="{ tooltip: '{{ $tooltip }}' }">
      <button x-tooltip="tooltip">
        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
      </button>
    </div>
  </div>

  @if ($wire_lazy === true)
  <x-input-group-end :wire:model.lazy='$name' :value="old($name)" :$id :$name :$has_error :$suffix :$maxlength :$required :$placeholder x-data="" :x-mask="$mask"/>
  @elseif ($wire_lazy === false)
  <x-input-group-end :wire:model='$name' :value="old($name)" :$id :$name :$has_error :$suffix :$maxlength :$required :$placeholder x-data="" :x-mask="$mask"/>
  @else
  <x-input-group-end :value="old($name)" :$id :$name :$has_error :$suffix :$maxlength :$required :$placeholder x-data="" :x-mask="$mask"/>
  @endif

  @error('fqsd')
  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
  @enderror

  @error($name)
  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
  @enderror
</div>