<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Filament\Resources\AccessResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccess extends CreateRecord
{
    protected static string $resource = AccessResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if (method_exists(static::getModel(), 'driver') && method_exists(static::getModel(), 'vehicle')) {
            \dd('hm',  $this->form->getLivewire()->data, auth()->id(), $data);
            $vehicleData =  $this->form->getLivewire()->data['vehicle'];
            $driverData =  $this->form->getLivewire()->data['driver'];

            // create or find vehicle
            // create or find driver
            // create access, attach driver and vehicle models and set it's status
            // if it's status is set to issued and send access sms is true, then emit sendAccessSMSEvent

            // try {
            //     $access = static::getModel()::create($data);
            // } catch (\Throwable $th) {
            // }
        }


        \dd('h',  $this->form->getLivewire()->data, auth()->id(), $data);

        return static::getModel()::create($data);
    }

    // protected function afterCreate(): void
    // {
    //     \dd('afterCreate', $this->form->getLivewire()->data);
    // }
}
