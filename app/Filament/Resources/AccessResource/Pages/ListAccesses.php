<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Filament\Resources\AccessResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Position;

class ListAccesses extends ListRecords
{
    protected static string $resource = AccessResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTablePollingInterval(): ?string
    {
        return '30s';
    }

    protected function getTableActionsPosition(): ?string
    {
        return Position::BeforeCells;
    }
}
