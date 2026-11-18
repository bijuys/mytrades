<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeConfirmEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $confirmUrl
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to MyTrades! Please confirm your email to begin your trading journey',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-confirm',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
