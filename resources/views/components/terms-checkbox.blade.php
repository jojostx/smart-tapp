@props(['disabled' => false, 'id' = 'terms', 'terms_route'])

<div class="flex items-center">
  <input id="{{ $id }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-4 h-4 text-blue-600 bg-gray-100 rounded']) !!} type="checkbox">
  <label for="{{ $id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ __('I agree with the') }} <a target="_blank" href="{{ $terms_route ?? '#' }}" class="text-blue-600 hover:underline">{{ __('terms and conditions') }}</a>.</label>
</div>
