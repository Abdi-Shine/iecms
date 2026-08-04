<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
class AccessGrantedMail extends Mailable
{

    public function __construct(
        public string $empName,
        public string $username,
        public string $password,
        public string $empEmail,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Koontadaada IECMS - Xogta Gelitaanka Nidaamka',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.access_granted',
        );
    }
}
