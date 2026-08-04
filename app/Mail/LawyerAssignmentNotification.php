<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LawyerAssignmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $lawyerName,
        public string $fileNo,
        public string $courtName,
        public string $caseType,
        public string $partyRole,
        public string $assignmentDate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ogeysiis Ku Saabsan Dacwad Loo Qoondeeyay Difaac Qareen',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lawyer_assignment_notification',
        );
    }
}
