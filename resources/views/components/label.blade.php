@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block mb-1 text-sm font-medium text-gray-900']) }}>
    {{ $value ?? $slot }}

    @if ($required)
        <span class="text-danger-600">*</span>    
    @endif
</label>
