@props(['disabled' => false, 'name' => ''])

<input {{ $disabled ? 'disabled' : '' }} {{ $attributes->class(['bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 border-gray-300 placeholder:text-gray-400 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50', 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 focus:ring-red' =>  $errors->has($name)])->merge(['name' => $name]) }} >
