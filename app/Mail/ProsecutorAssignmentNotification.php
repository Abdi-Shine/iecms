<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProsecutorAssignmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $prosecutorName,
        public string $caseNumber,
        public string $caseTitle,
        public string $roleLabel,
        public string $assignedDate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ogeysiis Ku Saabsan Xilsaarista Xeer Ilaaliye',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prosecutor_assignment_notification',
        );
    }
}
