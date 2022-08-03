<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Models\UserAccountStatus;
use App\Filament\Resources\UserResource;
use App\Models\Tenant\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function afterCreate(): void
    {
        if ($this->record instanceof User) {
            $this->record->sendCreateNewPasswordNotification();

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Str::random(40);
        $data['status'] = UserAccountStatus::INACTIVE;

        return $data;
    }
}
