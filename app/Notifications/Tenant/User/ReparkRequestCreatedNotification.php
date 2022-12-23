<?php

namespace App\Notifications\Tenant\User;

use App\Filament\Notifications\Notification as NotificationsNotification;
use App\Models\Tenant\ReparkRequest;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReparkRequestCreatedNotification extends Notification
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
            'title' => 'Repark Requested',
            'content' => "Driver of Vehicle **[{$blockee_plate_number}]** requested a repark for Vehicle **[{$blocker_plate_number}]**",
        ];
    }

    public function toDatabase($notifiable): array
    {
        $blockee_plate_number = $this->reparkRequest->blockeeVehicle->plate_number;
        $blocker_plate_number = $this->reparkRequest->blockerVehicle->plate_number;

        return NotificationsNotification::make()
            ->title('Repark Requested')
            ->body("Driver of Vehicle **[{$blockee_plate_number}]** requested a repark for Vehicle **[{$blocker_plate_number}]**")
            ->icon('heroicon-o-exclamation')
            ->danger()
            ->actions([
                Action::make('view')
                    ->url(route('filament.resources.tenant/repark-requests.index', ['tableSearchQuery' => $this->reparkRequest->uuid])),
            ])
            ->getDatabaseMessage();
    }
}
