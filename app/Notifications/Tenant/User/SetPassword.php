<?php

declare(strict_types=1);

namespace App\Notifications\Tenant\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $token,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl(mixed $notifiable): string
    {
        if (tenant()) {
            return tenant_route(
                tenant()->domain,
                'filament.auth.password.reset',
                [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ],
            );
        }

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function requestUrl(): string
    {
        if (tenant()) {
            return tenant_route(
                tenant()->domain,
                'filament.auth.password.request'
            );
        }

        return '';
    }

    /**
     * Get the set password notification mail message for the given URL.
     */
    protected function buildMailMessage(string $url): MailMessage
    {
        $host = parse_url($this->requestUrl())['host'];

        return (new MailMessage)
            ->subject(__('passwords.notifications.password_set.title', [
                'tenant' => $host,
            ]))
            ->markdown('emails.password-set', [
                'url' => $url,
                'tenant' => $host,
                'request_link' => $this->requestUrl(),
            ]);
    }
}
