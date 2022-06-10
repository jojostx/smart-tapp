@props(['disabled' => false, 'name' => ''])

<div class="relative"  x-data="{ show: true }">
  <input {{ $disabled ? 'disabled' : '' }} {{ $attributes->class(['pr-10 bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 border-gray-300 placeholder:text-gray-400 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50', 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red' =>  $errors->has($name)])->merge(['name' => $name]) }} x-bind:type="show ? 'password' : 'text'"/>
  
  <button class="absolute inset-y-0 right-0 flex items-center pr-3" type="button" @click.prevent="show = !show">
    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <g :class="{ 'hidden' : !show, 'inline-flex' : show }">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
      </g>
      <g :class="{ 'hidden': show, 'inline-flex' : !show }" x-cloak>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
      </g>
    </svg>
  </button>
</div>