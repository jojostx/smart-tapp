<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Models\UserAccountStatus;
use App\Filament\Resources\UserResource;
use App\Models\Tenant\User;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        $actions = [
            Actions\DeleteAction::make(),
        ];

        if ($this->record instanceof User && $this->record->isInactive()) {
            $actions = array_merge(
                [
                    $this->resendAccountActivationEmailAction()
                ],
                $actions
            );
        }

        return $actions;
    }

    public function resendAccountActivationEmailAction()
    {
        $action = Action::make('resend')
            ->label('Send Activation Email')
            ->action(function () {
                dd('resent');
            });

        return $action;
    }

    public function afterSave(): void
    {
        if (!$this->record instanceof User) {
            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
