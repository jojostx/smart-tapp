<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Tenant\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function afterCreate(): void
    {
        if (! $this->record instanceof User) {
            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
