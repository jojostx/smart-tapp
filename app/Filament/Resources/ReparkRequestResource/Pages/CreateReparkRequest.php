<?php

namespace App\Filament\Resources\ReparkRequestResource\Pages;

use App\Filament\Resources\ReparkRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateReparkRequest extends CreateRecord
{
    protected static string $resource = ReparkRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        \dd($data);

        return $this->getModel()::create($data);
    }
}
