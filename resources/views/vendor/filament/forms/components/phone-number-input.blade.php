@php
    $affixLabelClasses = [
        'whitespace-nowrap group-focus-within:text-primary-500',
        'text-gray-400' => ! $errors->has($getStatePath()),
        'text-danger-400' => $errors->has($getStatePath()),
    ];
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div {{ $attributes->merge($getExtraAttributes())->class(['flex items-center space-x-2 rtl:space-x-reverse group filament-forms-text-input-component']) }}>
        @if (($prefixAction = $getPrefixAction()) && (! $prefixAction->isHidden()))
            {{ $prefixAction }}
        @endif

        @if ($icon = $getPrefixIcon())
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
        @endif

        @if ($label = $getPrefixLabel())
            <span @class($affixLabelClasses)>
                {{ $label }}
            </span>
        @endif

        <div class="flex-1">
            <input
                x-init="
                    $watch('state', (value) => { console.log(value) });
                    $nextTick(() => { 
                            if ($el.type === 'tel') {
                                itl = intlTelInput($el, {
                                    utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.16/js/utils.js',
                                    initialCountry: 'NG',
                                    onlyCountries: allowedCountries,
                                    customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                                        return 'e.g. ' + selectedCountryPlaceholder;
                                    }
                                });

                                itl.setNumber(state);
                            }
                    });"
                x-data="{ 
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }}.defer,
                    itl: null,
                    allowedCountries: {{ json_encode($getAllowedCountries()) }}
                }"
                x-model.lazy="state"
                {!! ($type = $getType()) ? "type=\"{$type}\"" : 'tel' !!}
                {{ $getExtraAlpineAttributeBag() }}
                dusk="filament.forms.{{ $getStatePath() }}"
                {!! ($autocomplete = $getAutocomplete()) ? "autocomplete=\"{$autocomplete}\"" : null !!}
                {!! $isAutofocused() ? 'autofocus' : null !!}
                {!! $isDisabled() ? 'disabled' : null !!}
                id="{{ $getId() }}"
                {!! "inputmode=\"{$getType()}\"" !!}
                {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                {!! $isRequired() ? 'required' : null !!}
                {{ $getExtraInputAttributeBag()->class([
                    'block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70',
                    'dark:bg-gray-700 dark:text-white dark:focus:border-primary-600' => config('forms.dark_mode'),
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'dark:border-gray-600' => (! $errors->has($getStatePath())) && config('forms.dark_mode'),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                ]) }}
            />
        </div>

        @if ($label = $getSuffixLabel())
            <span @class($affixLabelClasses)>
                {{ $label }}
            </span>
        @endif

        @if ($icon = $getSuffixIcon())
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
        @endif

        @if (($suffixAction = $getSuffixAction()) && (! $suffixAction->isHidden()))
            {{ $suffixAction }}
        @endif
    </div>
</x-dynamic-component>
