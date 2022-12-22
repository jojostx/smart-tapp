<?php

namespace App\Filament\Resources\ParkingLotResource\Widgets;

use App\Filament\Forms\Components\Qrcode;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

class QrcodeWidget extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::widgets.qrcode-widget';

    public ?Model $record = null;

    protected function getFormSchema(): array
    {
        return [
            Qrcode::make('qrcode')
                ->disableLabel()
                ->helperText('<span class="text-xs"><span class="text-sm">&#9432;</span> The Qrcode will have dimensions: [500 X 500] pixels.</span>')
                ->columnSpan('full')
                ->content(fn () => $this->record ? $this->record->qrcode : '-')
                ->downloadName(fn () => $this->record ? $this->record->name : '')
        ];
    }
}
