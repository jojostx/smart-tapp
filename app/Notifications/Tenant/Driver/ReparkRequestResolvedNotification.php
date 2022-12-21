<?php

namespace App\Notifications\Tenant\Driver;

use App\Models\Tenant\ReparkRequest;
use App\Models\Tenant\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use ManeOlawale\Laravel\Termii\Messages\Message as TermiiMessage;
use NotificationChannels\AfricasTalking\AfricasTalkingChannel;
use NotificationChannels\AfricasTalking\AfricasTalkingMessage;

class ReparkRequestResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public ReparkRequest $reparkRequest,
        public ?User $admin = null,
        ?string $id = '',
        public int $checkStatusCountdown = 0
    ) {
        if (filled($id)) {
            $this->id = $id;
        }

        if (blank($admin)) {
            $this->admin = auth()->user();
        }

        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', AfricasTalkingChannel::class];
    }

    /**
     * Get the notification's fallback delivery channels.
     * This is used to resend the notification in an event of failed.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function fallbackChannels($notifiable)
    {
        return ['termii'];
    }

    /**
     * Get the AfricasTalking SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return AfricasTalkingMessage
     */
    public function toAfricasTalking($notifiable)
    {
        $message = $this->buildSMSMessage($notifiable);

        return (new AfricasTalkingMessage())
          ->content($message);
    }

    /**
     * Get the termii SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \ManeOlawale\Laravel\Termii\Messages\Message
     */
    public function toTermii($notifiable)
    {
        $message = $this->buildSMSMessage($notifiable);

        return (new TermiiMessage())
          ->content($message);
    }

    /**
     * Get the verify SMS notification message for the given URL.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function buildSMSMessage($notifiable): string
    {
        $plate_number = $this->reparkRequest->blockerVehicle->plate_number;

        $message = "Hello, Vehicle with plate_number [{$plate_number}] has been reparked";
        $message .= "\nYou can now move your vehicle. Thanks";

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'admin_id' => $this->admin->id,
            'repark_request_id' => $this->reparkRequest->id,
            'notification' => serialize($this),
            'checkStatusCountdown' => $this->getCheckStatusCountdown(),
        ];
    }

    public function failed(\Exception $exception)
    {
        if ($exception instanceof GuzzleException) {
            // set the status of the notification to failed in the notifications table:
            /** @var \Illuminate\Database\Eloquent\Model|null $notification */
            $notification = DatabaseNotification::find($this->id);

            if (filled($notification)) {
                $notification->forceFill([
                    'data->status' => 'failed',
                ])->save();
            }
        }
    }

    protected function getCheckStatusCountdown()
    {
        return ((bool) $this->checkStatusCountdown && $this->checkStatusCountdown < 30) ? 30 : $this->checkStatusCountdown;
    }
}
