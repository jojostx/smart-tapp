@switch($getType())
    @case('grouped')
        <!-- grouped-action -->
        <x-tables::actions.action
            :action="$action"
            :icon="$action->getGroupedIcon()"
            component="tables::dropdown.item"
            class="filament-tables-grouped-action"
            x-on:click.prevent="$clipboard('{{ $getContent() }}'); $tooltip('{{ $getSuccessMessage() }}')"
        >
            {{ $getLabel() }}
        </x-tables::actions.action>
        @break
 
    @case('iconButton')
        <!-- icon-button-action -->
        <x-tables::actions.action
            :action="$action"
            :label="$getLabel()"
            component="tables::icon-button"
            class="-my-2 filament-tables-icon-button-action"
            x-on:click.prevent="$clipboard('{{ $getContent() }}'); $tooltip('{{ $getSuccessMessage() }}')"
        />
        @break
 
    @default
        <!-- button-action -->
        <x-tables::actions.action
            :action="$action"
            :outlined="$isOutlined()"
            :icon-position="$getIconPosition()"
            component="tables::button"
            class="filament-tables-button-action"
            x-on:click.prevent="$clipboard('{{ $getContent() }}'); $tooltip('{{ $getSuccessMessage() }}')"
        >
            {{ $getLabel() }}
        </x-tables::actions.action>
@endswitch