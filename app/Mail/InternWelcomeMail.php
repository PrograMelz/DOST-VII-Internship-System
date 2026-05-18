<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InternWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $username,
        public string $password,
        public ?string $fromName = null
    ) {}

    public function envelope(): Envelope
    {
        $name = $this->fromName ?? config('mail.from.name');

        return new Envelope(
            from: new Address(config('mail.from.address'), $name),
            subject: 'Welcome – Your intern account credentials',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.intern-welcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
