<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use App\Filament\Traits\CanCleanupStaleRecords;
use App\Filament\Traits\WithCurrentPasswordField;
use App\Models\Tenant\Vehicle;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditVehicle extends EditRecord
{
    use CanCleanupStaleRecords;
    use WithCurrentPasswordField;

    protected static string $resource = VehicleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalSubheading(function (): string {
                    return "Are you sure you want to do this? Doing so will delete all Accesses assigned to it and any associated Driver if they have no related Accesses.";
                })
                ->form([
                    static::getCurrentPasswordField(),
                ])
                ->action(function (Vehicle $record, \Filament\Pages\Actions\DeleteAction $action): void {
                    $wasSuccessful = DB::transaction(function () use ($record) {
                        // 1. delete all associated drivers if the driver has no other related accesses
                        $record->drivers()->each(function ($driver) {
                            return static::cleanupstaleRecords($driver, [], false);
                        });

                        // 2. delete $record && delete all related accesses, (instead of running [$record->accesses->each->delete()] implicitly executed on a db level due to onCascadeDelete option)
                        return $record->delete();
                    });

                    $wasSuccessful ? $action->success() : $action->failure();
                })
                ->modalWidth('md'),
        ];
    }
}
