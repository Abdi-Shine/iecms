<?php

namespace App\Notifications;

use App\Models\Hearing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StampApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Hearing $hearing,
        public string  $approverName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $case = $this->hearing->civilCase;
        return [
            'type'         => 'stamp_approved',
            'title'        => 'Summadda La Ogolaaday — ' . ($case->FileNo ?? ''),
            'body'         => $this->approverName . ' wuxuu ogolaaday summadda dhageysiga ' . $this->hearing->hearing_date->format('d/m/Y'),
            'hearing_id'   => $this->hearing->id,
            'file_no'      => $case->FileNo ?? '',
            'hearing_date' => $this->hearing->hearing_date->format('d/m/Y'),
            'link'         => route('hearings.document', $this->hearing->id),
        ];
    }
}
