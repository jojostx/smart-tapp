<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }} }" class="flex items-center" style="margin-top: 18px; margin-bottom: 18px;">
        <input
            id="{{$getId()}}"
            type="range"
            x-model="state"
            class="mr-2 border-gray-300 focus:outline-none focus:bg-primary-200 dark:focus:bg-primary-900 disabled:opacity-70 disabled:cursor-not-allowed filament-forms-range-component dark:bg-white/10 w-90"
            {!! $isRequired() ? 'required' : null !!}
            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
            min="{{ $getMin() }}"
            max="{{ $getMax() }}"
            step="{{ $getStep() }}"
            dusk="filament.forms.{{ $getStatePath() }}"
            {!! $isDisabled() ? 'disabled' : null !!}
        />

        <span class="filament-forms-range-component__value" x-text="state ?? 0"></span>
    </div>
</x-forms::field-wrapper>
