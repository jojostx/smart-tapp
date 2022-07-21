@props(['disabled' => false, 'name' => ''])

<div class="relative"  x-data="{ show: true }">
  <input {{ $disabled ? 'disabled' : '' }} {{ $attributes->class(['pr-10 bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 border-gray-300 placeholder:text-gray-400 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50', 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red' =>  $errors->has($name)])->merge(['name' => $name]) }} x-bind:type="show ? 'password' : 'text'"/>
  
  <button class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400" type="button" @click.prevent="show = !show">
    <x-heroicon-o-eye class="w-5 h-5" ::class="{ 'hidden': show, 'inline-flex' : !show }" x-cloak/>
    <x-heroicon-o-eye-off class="w-5 h-5" ::class="{ 'hidden': !show, 'inline-flex' : show }" x-cloak/>
  </button>
</div>