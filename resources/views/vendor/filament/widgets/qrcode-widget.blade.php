<x-filament::modal @click.outside="$dispatch('close-modal', {id: 'qrcode-modal'})" width="md" id="qrcode-modal" heading="Download Qrcode">
    <div class="relative bg-white rounded-lg dark:bg-gray-700">
        {{ $this->form }}
    </div>
</x-filament::modal>
