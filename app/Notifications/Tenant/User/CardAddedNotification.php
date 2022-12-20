<?php

namespace App\Notifications\Tenant\User;

use App\Models\CreditCard;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CardAddedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public CreditCard $creditCard)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('Card addition successful')
            ->body("Congratulations 🎉 your card was successfully added :)")
            ->success()
            ->getDatabaseMessage();
    }
}
