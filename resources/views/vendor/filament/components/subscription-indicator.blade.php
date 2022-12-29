<div class="text-xs">
    @if(filled($this->tooltip))    
        <div class="flex justify-center items-center mr-2 text-xs cursor-pointer" x-data="{ tooltip: '{{ $this->tooltip }}' }">
            <button x-tooltip="tooltip">
                <x-dynamic-component :component="$this->icon" class="w-8 h-8 text-{{ $this->color }}-500" />
            </button>
        </div>
    @endif
</div>
