<?php

namespace App\Filament\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class NotificationsPanel extends Component
{
    protected static string $view = 'filament::components.notifications-panel';

    public function getUnreadNotifications(): ?Collection
    {
      return \collect([1]);
      // return auth()->user()->unreadNotifications;
    }

    public function getHasUnreadNotificationsProperty(): bool
    {
      return auth()->user()->unreadNotifications()->exists();
    }

    public function getViewData(): array
    {
      return [
        'unreadNotifications' => $this->getUnreadNotifications()
      ];
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData());
    }
}
