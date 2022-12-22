<?php

namespace App\Notifications\Tenant\User;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Jojostx\Larasubs\Models\Subscription;

class SubscriptionSuccessfulNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Subscription $subscription)
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
            ->title('Plan checkout successful')
            ->body("Congratulations 🎉 on subscribing to the **{$this->subscription->plan->name}** Plan. Start Managing Your Parking Lots :)")
            ->success()
            ->getDatabaseMessage();
    }
}
