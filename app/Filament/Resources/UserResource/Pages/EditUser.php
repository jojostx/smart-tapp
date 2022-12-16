<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Traits\WithCurrentPasswordField;
use App\Models\Tenant\User;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    use WithCurrentPasswordField;

    protected static string $resource = UserResource::class;

    protected function getListeners()
    {
        return ['$refresh'];
    }

    protected function getActions(): array
    {
        $actions = [
            Actions\DeleteAction::make(),
        ];

        $user = $this->record;

        if ($user instanceof User) {
            $actions = array_merge(
                [
                    Action::make('resend')
                        ->label('Send Activation Email')
                        ->visible(fn () => $user->isInactive())
                        ->action(function () use ($user) {
                            $user->sendCreateNewPasswordNotification();
                            app(PermissionRegistrar::class)->forgetCachedPermissions();
                            $this->emitSelf('$refresh');
                        }),
                    Action::make('toggle_status')
                        ->label(fn () => $user->isActive() ? 'Deactivate' : 'Activate')
                        ->button()
                        ->action(function () use ($user) {
                            $user->isActive() ? $user->deactivateAccount() : $user->activateAccount();
                            app(PermissionRegistrar::class)->forgetCachedPermissions();
                            $this->emitSelf('$refresh');
                        })
                        ->requiresConfirmation()
                        ->form([
                            static::getCurrentPasswordField(),
                        ])
                        ->color(fn () => $user->isActive() ? 'warning' : 'primary'),
                ],
                $actions
            );
        }

        return $actions;
    }

    public function afterSave(): void
    {
        if (! $this->record instanceof User) {
            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
