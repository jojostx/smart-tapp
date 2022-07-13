<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Filament\Components\RefreshListPageTableComponent;
use App\Filament\Resources\AccessResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccesses extends ListRecords
{
    protected static string $resource = AccessResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // FilamentInfoWidget::class,
            RefreshListPageTableComponent::class,
        ];
    }
}
