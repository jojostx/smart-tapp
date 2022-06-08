@props(['suffix', 'disabled' => false])

<div>
  <div class="relative text-gray-400 focus-within:text-indigo-400">
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'placeholder:text-gray-400 block mr-1 border w-full border-gray-300 bg-gray-50 text-gray-900 text-sm rounded-lg p-2.5 pr-9 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 focus-visible:border-indigo-300 outline-none focus-visible:ring-indigo-200 focus-visible:ring-opacity-50']) !!}>

    <span class="absolute inset-y-0 right-0 flex items-center px-3 text-sm bg-white border border-gray-300 rounded-lg rounded-l-none">
      {{ $suffix }}
    </span>
  </div>
</div>