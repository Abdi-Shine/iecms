<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CivilCasePartyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $partyName,
        public string $fileNo,
        public string $courtName,
        public string $caseType,
        public string $openDate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ogeysiis Diiwaangelinta Dacwadda Maxkamadda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.civil_case_party_notification',
        );
    }
}
