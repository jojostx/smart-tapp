<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecoverOldEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $pendingUserEmail;

    /**
     * @var Carbon
     */
    public $changed_at;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Model $pendingUserEmail, Carbon $changed_at)
    {
        $this->pendingUserEmail = $pendingUserEmail;
        $this->changed_at = $changed_at;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Your email address has been changed',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.recover-old-email',
            with: ['url' => $this->pendingUserEmail->verificationUrl()]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
