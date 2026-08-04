<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class BackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $filePath,
        public string $fileName,
        public string $dbSize,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'IECMS Database Backup — ' . now()->format('d M Y H:i'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.backup');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)->as($this->fileName)->withMime('application/sql'),
        ];
    }
}
