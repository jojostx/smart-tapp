@props([
    'details' => [],
    'title',
    'identifier',
    'wireClickEvent'
])

@php
    $wireClickAction = "mountInboxAction({$wireClickEvent}, '{$identifier}')";
@endphp

<li {{ $attributes->class(['filament-inbox-search-result']) }}>
    <div class="relative block focus:bg-gray-500/5 hover:bg-gray-500/5 focus:ring-1 focus:ring-gray-300">
        <button
            x-on:click="$dispatch('close-inbox-search-results')"
            wire:click="{{ $wireClickAction }}"
            wire:loading.attr="disabled"
            class="w-full flex items-center p-2 py-4 transition rounded-lg cursor-pointer">
            <div style="background-image: url('{{ getUiAvatarUrl($details['Name']) }}')" class="w-8 h-8 rounded-full bg-gray-200 bg-cover bg-center dark:bg-gray-900"></div>
            <div class="ml-2 text-left">
                <p class="text-sm text-gray-700 font-semibold capitalize">{{ $details['Name'] }}</p>
                <p class="text-xs font-medium text-gray-600">{{ $details['Phone_number'] ?? $details['Email'] }}</p>
            </div>
        </button>
    </div>
</li>
