@props(['suffix', 'disabled' => false, 'name' => ''])

<div>
  <div class="relative text-gray-400 focus-within:text-indigo-400">
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->class(['placeholder:text-gray-400 block mr-1 border w-full border-gray-300 bg-gray-50 text-gray-900 text-sm rounded-lg p-2.5 pr-9 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 focus-visible:border-indigo-300 outline-none focus-visible:ring-indigo-200 focus-visible:ring-opacity-50', 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red' =>  $errors->has($name)])->merge(['name' => $name]) !!}>

    <span 
      @class([
        "absolute inset-y-0 right-0 flex items-center px-3 text-sm bg-white border border-gray-300 rounded-lg rounded-l-none",
        'border-red-300 text-red-900' =>  $errors->has($name),
      ])>
      {{ $suffix }}
    </span>
  </div>
</div>