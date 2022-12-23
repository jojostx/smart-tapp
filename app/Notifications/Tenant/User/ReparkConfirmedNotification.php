<?php

namespace App\Notifications\Tenant\User;

use App\Filament\Notifications\Notification as NotificationsNotification;
use App\Models\Tenant\ReparkRequest;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReparkConfirmedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public ReparkRequest $reparkRequest)
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $blockee_plate_number = $this->reparkRequest->blockeeVehicle->plate_number;
        $blocker_plate_number = $this->reparkRequest->blockerVehicle->plate_number;

        return [
            'repark_request_id' => $this->reparkRequest->id,
            'repark_request_uuid' => $this->reparkRequest->uuid,
            'title' => 'Repark Confirmed',
            'content' => "The vehicle **[{$blocker_plate_number}]** blocking vehicle **[{$blockee_plate_number}]** has been reparked.",
        ];
    }

    public function toDatabase($notifiable): array
    {
        $blockee_plate_number = $this->reparkRequest->blockeeVehicle->plate_number;
        $blocker_plate_number = $this->reparkRequest->blockerVehicle->plate_number;

        return NotificationsNotification::make()
            ->title('Repark Confirmed')
            ->body("The vehicle **[{$blocker_plate_number}]** blocking vehicle **[{$blockee_plate_number}]** has been reparked.")
            ->success()
            ->actions([
                Action::make('view')
                    ->url(route('filament.resources.tenant/repark-requests.index', ['tableSearchQuery' => $this->reparkRequest->uuid])),
            ])
            ->getDatabaseMessage();
    }
}
