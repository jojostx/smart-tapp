@props(['suffix', 'disabled' => false, 'name' => '', 'has_error' => false, 'value' => ''])

<div>
  <div class="relative text-gray-400 focus-within:text-primary-400">
    <input {{ $disabled ? 'disabled' : '' }} 
    {!! 
      $attributes
        ->class([
        'placeholder:text-gray-400 block mr-1 border w-full border-gray-300 bg-gray-50 text-gray-900 text-sm rounded-lg p-2.5 pr-9 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 focus-visible:border-primary-300 outline-none focus-visible:ring-primary-200 focus-visible:ring-opacity-50', 
        'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red' =>  $has_error
        ])
        ->merge(['name' => $name, 'value' => $value]) 
    !!}>

    <span 
      @class([
        "absolute inset-y-0 right-0 flex items-center px-3 text-sm bg-white border border-gray-300 rounded-lg rounded-l-none",
        'border-red-300 text-red-900' => $has_error,
      ])>
      {{ $suffix }}
    </span>
  </div>
</div>