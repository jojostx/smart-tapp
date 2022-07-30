<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Models\UserAccountStatus;
use App\Filament\Resources\UserResource;
use App\Models\Tenant\User;
use App\Notifications\Tenant\User\SetPassword;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function afterCreate(): void
    {
        $user = $this->record;

        if (! $user instanceof User) {
            return;
        }

        /**
         * @var \Illuminate\Auth\Passwords\PasswordBroker $broker
         */
        $broker = Password::broker();

        $token = $broker->createToken($user);
        
        $user->notify(new SetPassword($token));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Str::random(40);
        $data['status'] = UserAccountStatus::INACTIVE;

        return $data;
    }
}
