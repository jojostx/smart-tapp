<x-filament::card>
    <div {{ $attributes->merge($getExtraAttributes())->class('border rounded-xl my-2 p-4 border-primary-400 grid grid-cols-12 gap-4 filament-forms-placeholder-component') }}>
        <div class="col-span-1 text-primary-700">
            <x-dynamic-component :component="$getIcon()" class="shrink-0 min-w-[26px]" />
        </div>
        <div class="col-span-11">
          {{ $getContent() }}
        </div>
    </div>
</x-filament::card>