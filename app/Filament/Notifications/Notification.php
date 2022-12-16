<?php

namespace App\Filament\Notifications;

use Filament\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
{
    public function getActions(): array
    {
        return $this->evaluate($this->actions, ['duration' => $this->getDuration()]);
    }
}
