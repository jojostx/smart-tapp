@props([
    'activeMenu',
    'title',
    'messageables',
    'selectedMessageable',
    'wireClickEvent',
    'sumAttribute',
])

<div 
    x-data="{ activeMenu: '{{ $activeMenu }}' }"
    class="flex flex-col flex-grow">
    <div class="border-b px-4 py-2">
        <p class="font-medium text-xs text-gray-500">{{ $title }}</p>
    </div>

    @foreach ($messageables as $group => $groupedMessageables)
        <x-filament::inbox.messageable.group 
            :label="$group"
            :messageables="$groupedMessageables" 
            :$selectedMessageable
            :$wireClickEvent
            :sum="filled($sumAttribute) ? $groupedMessageables->sum($sumAttribute) : null"
        />
    @endforeach
</div>