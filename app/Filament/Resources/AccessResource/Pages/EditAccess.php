<?php

namespace App\Filament\Resources\AccessResource\Pages;

use App\Enums\Models\AccessStatus;
use App\Filament\Notifications\Notification;
use App\Filament\Resources\AccessResource;
use App\Filament\Traits\WithCurrentPasswordField;
use Filament\Forms;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAccess extends EditRecord
{
    use WithCurrentPasswordField;

    protected static string $resource = AccessResource::class;

    protected function getListeners()
    {
        return ['$refresh'];
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('issue')
                ->label('Issue Activation')
                ->visible(fn () => $this->getRecord()->isExpired() || $this->getRecord()->isInactive())
                ->color('warning')
                ->icon('heroicon-o-arrow-circle-up')
                ->tooltip('Issue Access Activation')
                ->requiresConfirmation()
                ->modalHeading(function (): string {
                    return 'Issue Access Activation';
                })
                ->modalSubheading(function (): string {
                    return "This will re-issue the Access without activating it and by default, 
                    the Access Activation Notification will be sent to the Driver's phone number.";
                })
                ->form([
                    Forms\Components\Checkbox::make('shouldNotify')
                        ->label('Send Activation Notification')
                        ->default(true),
                ])
                ->action('issueAccess'),

            Actions\Action::make('send')
                ->label('Send Activation')
                ->visible(fn () => $this->getRecord()->isIssued() || $this->getRecord()->isActive())
                ->color('primary')
                ->icon('heroicon-o-paper-airplane')
                ->tooltip('Send Activation Notification')
                ->requiresConfirmation()
                ->modalHeading(function (): string {
                    return 'Send Activation Notification';
                })
                ->modalSubheading(function (): string {
                    return "This will send the Access Activation Notification to the Driver's phone.";
                })
                ->form([
                    static::getCurrentPasswordField(),
                ])
                ->action('sendActivationNotification'),

            Actions\Action::make('activate')
                ->visible(function () {
                    $record = $this->getRecord();

                    return $record->isExpired() ||
                        $record->isInactive() ||
                        $record->isIssued();
                })
                ->color('primary')
                ->tooltip('Activate Access')
                ->requiresConfirmation(fn () => !$this->getRecord()->isActive())
                ->modalHeading(function (): string {
                    return 'Activate Access';
                })
                ->modalSubheading(function (): string {
                    $record = $this->getRecord();
                    $a = ($record->isExpired() || $record->isIssued()) ? 'not' : '';

                    return "This will activate the Access and allow the Driver to access their dashboard.
                    By default, the Access Activation Notification will {$a} be sent to the Driver's phone number.";
                })
                ->form([
                    Forms\Components\Checkbox::make('shouldNotify')
                        ->label('Send Activation Notification')
                        ->default(fn () => $this->getRecord()->isInactive() ? true : false),
                ])
                ->action('activateAccess'),

            Actions\Action::make('deactivate')
                ->visible(function () {
                    $record = $this->getRecord();

                    return $record->isExpired() ||
                        $record->isActive() ||
                        $record->isIssued();
                })
                ->color('warning')
                ->tooltip('Deactivate Access')
                ->requiresConfirmation()
                ->modalHeading(function (): string {
                    return 'Deactivate Access';
                })
                ->modalSubheading(function (): string {
                    return 'This will deactivate the Access and prevent the Driver from accessing their dashboard.';
                })
                ->form([
                    static::getCurrentPasswordField(),
                ])
                ->action('deactivateAccess'),

            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->form([
                    static::getCurrentPasswordField(),
                ])
                ->modalWidth('md'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($data['status'] == AccessStatus::INACTIVE->value) {
            $record->fill($data);
            $this->deactivateAccess();

            return $record;
        }

        if ($data['status'] == AccessStatus::ISSUED->value) {
            $record->fill($data);
            $this->issueAccess(array_merge($data, ['shouldNotify' => true]));

            return $record;
        }

        if ($data['status'] == AccessStatus::ACTIVE->value) {
            $record->fill($data);
            $this->activateAccess(array_merge($data, ['shouldNotify' => true]));

            return $record;
        }

        return $record;
    }

    public function sendActivationNotification()
    {
        $this->getRecord()->sendAccessActivationNotification(checkStatusCountdown: 30) &&
            Notification::make()
            ->body("The **Access Activation Notification** will be sent to the Driver's phone")
            ->success()
            ->send();

        $this->emitSelf('$refresh');
    }

    public function issueAccess(?array $data)
    {
        /** @var \App\Models\Tenant\Access */
        $record = $this->getRecord();
        $shouldNotify = isset($data['shouldNotify']) && $data['shouldNotify'];

        if ($record->issue() || $record->isIssued()) {
            $shouldNotify && $record->sendAccessActivationNotification(checkStatusCountdown: 30);

            Notification::make()
                ->title('Access Issued Successfully')
                ->body($shouldNotify ? "The **Access Activation Notification** will be sent to the Driver's phone" : null)
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Unable to Issue Access')
                ->danger()
                ->send();
        }

        $this->emitSelf('$refresh');
    }

    public function activateAccess(?array $data)
    {
        /** @var \App\Models\Tenant\Access */
        $record = $this->getRecord();

        if ($record->hasAnotherActiveAccessWithSameVehicle()) {
            Notification::make()
                ->body('Unable to activate because another Access already exists and has been issued for this Vehicle.')
                ->danger()
                ->send()
                ->seconds(30);

            return;
        }

        $shouldNotify = isset($data['shouldNotify']) && $data['shouldNotify'];

        if ($record->activate() || $record->isActive()) {
            $shouldNotify && $record->sendAccessActivationNotification(checkStatusCountdown: 30);

            Notification::make()
                ->title('Access Activated Successfully')
                ->body($shouldNotify ? "The **Access Activation Notification** will be sent to the Driver's phone" : null)
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Unable to Activate Access')
                ->danger()
                ->send();
        }

        $this->emitSelf('$refresh');
    }

    public function deactivateAccess()
    {
        $this->getRecord()->deactivate() &&
            Notification::make()
            ->title('Access Deactivated Successfully')
            ->success()
            ->send();

        $this->emitSelf('$refresh');
    }
}
