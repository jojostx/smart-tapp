<?php

namespace App\Filament\Components;

use App\Models\Tenant\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class NotificationsPanel extends Component
{
  protected static string $view = 'filament::components.notifications-panel';

  public function getAdminUserProperty(): ?User
  {
    return auth()->user();
  }

  public function getHasUnreadNotificationsProperty(): bool
  {
    $hasUnread = $this->adminUser->unreadNotifications()->exists();

    return $hasUnread;
  }

  public function getReadNotificationsProperty(): ?array
  {
    return $this->adminUser->readNotifications->pluck('id')->toArray();
  }

  public function getNotificationsProperty(): ?Collection
  {
    return $this->adminUser->notifications;
  }

  public function markNotificationAsRead($key)
  {
    $this->notifications->find($key)?->markAsRead();
  }

  public function markNotificationAsUnread($key)
  {
    $notification = $this->notifications->find($key);

    if (blank($notification)) {
      return;
    }

    $notification->read_at = null;

    $notification->save();
  }

  public function markAllNotificationsAsRead(): ?array
  {
    $this->notifications->markAsRead();

    return $this->notifications->pluck('id')->toArray();
  }

  public function getViewData(): array
  {
    return [
      'readNotifications' => $this->readNotifications,
      'notifications' => $this->notifications,
    ];
  }

  public function render(): View
  {
    return view(static::$view, $this->getViewData());
  }
}
