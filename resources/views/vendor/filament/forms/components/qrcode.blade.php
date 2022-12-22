<x-dynamic-component 
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :state-path="$getStatePath()">
    <div 
        x-init="$nextTick(() => { 
            let svg = $el.querySelector('#parkinglot_qrcode');
            let downloadlink = $el.querySelector('#download_qrcode_link');
            if (svg) {
                const width = 450;
                const height = 450;

                let clonedSvg = svg.cloneNode(true);
                clonedSvg.setAttribute('width', width);
                clonedSvg.setAttribute('height', height);

                let blob = new Blob([clonedSvg.outerHTML], {
                    type: 'image/svg+xml;charset=utf-8'
                });

                const URL = window.URL || window.webkitURL || window;
                let blobURL = URL.createObjectURL(blob);

                let image = new Image();
                image.onload = () => {
                    let canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    let context = canvas.getContext('2d');
                    context.drawImage(image, 0, 0, width, height);
                    downloadlink.href = canvas.toDataURL();
                };
                image.src = blobURL;
            }
        })"  
        {{ $attributes->merge($getExtraAttributes())->class('filament-forms-placeholder-component') }}>
        {{ $getContent() }}
        <div class="mt-4">
            <a id="download_qrcode_link" download="{{ $getDownloadName() }}.png">
                <x-filament::button>
                    {{ __('Download') }}
                </x-filament::button>
            </a>
        </div>
    </div>
</x-dynamic-component>