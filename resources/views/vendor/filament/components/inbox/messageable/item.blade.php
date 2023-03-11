@props([
    'messageable',
    'isSelected' => false,
    'wireClickEvent' => 'getMessageable'
])

@php
    $wireClickAction = "mountInboxAction('{$wireClickEvent}', '{$messageable->uuid}')";
@endphp

<li wire:key="user-{{ $messageable->uuid }}">
    <button
        wire:click="{{ $wireClickAction }}"
        wire:loading.attr="disabled"
        class="@if($isSelected) bg-primary-100 @endif hover:bg-primary-100 w-full flex items-center p-2 transition rounded-lg hover:cursor-pointer">
        <div style="background-image: url('{{ getUiAvatarUrl($messageable->name) }}')" class="w-8 h-8 rounded-full bg-gray-200 bg-cover bg-center dark:bg-gray-900"></div>
        <div class="ml-2 text-left">
            <p class="text-sm font-semibold capitalize">{{ $messageable->name }}</p>
            <p class="text-xs font-medium text-gray-600">{{ $messageable->email ?? $messageable->phone_number }}</p>
        </div>
        @if ($messageable->sent_messages_count)
            <span class="flex items-center justify-center w-4 h-4 ml-auto text-xs leading-none text-white bg-primary-500 rounded-full">
                {{ $messageable->sent_messages_count }}
            </span>
        @endif
    </button>
</li>
