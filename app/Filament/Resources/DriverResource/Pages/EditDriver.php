<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Resources\DriverResource;
use App\Filament\Traits\WithCurrentPasswordField;
use App\Models\Tenant\Driver;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditDriver extends EditRecord
{
    use WithCurrentPasswordField;

    protected static string $resource = DriverResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalSubheading(function (): string {
                    return 'Are you sure you want to do this? Doing so will delete all Accesses assigned to it and any associated Vehicle if they have no related Accesses.';
                })
                ->form([
                    static::getCurrentPasswordField(),
                ])
                ->action(function (Driver $record, \Filament\Pages\Actions\DeleteAction $action): void {
                    $wasSuccessful = DB::transaction(function () use ($record) {
                        // 1. delete all associated vehicles if the vehicle has no other related accesses
                        $record->vehicles()->each(function ($driver) {
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
