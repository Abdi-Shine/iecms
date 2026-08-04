<?php

namespace App\Notifications;

use App\Models\DistrictCriminalHearing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CriminalStampRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public DistrictCriminalHearing $hearing,
        public string                $requesterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $case = $this->hearing->criminalCase;
        return [
            'type'           => 'criminal_stamp_requested',
            'title'          => 'Codsiga Summadda — ' . ($case->FileNo ?? ''),
            'body'           => $this->requesterName . ' wuxuu codsaday summadda dhageysiga ' . $this->hearing->hearing_date->format('d/m/Y'),
            'hearing_id'     => $this->hearing->id,
            'file_no'        => $case->FileNo ?? '',
            'hearing_date'   => $this->hearing->hearing_date->format('d/m/Y'),
            'link'           => route('criminal-hearings.approval.stamp', $this->hearing->id),
        ];
    }
}
