<?php

namespace App\Notifications;

use App\Models\DistrictCriminalHandover;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CriminalHandoverSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public DistrictCriminalHandover $handover,
        public string                 $fileNo,
        public string                 $submitterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'criminal_handover_submitted',
            'title'        => 'Dhaqdhaqaaqa Cusub — ' . $this->fileNo,
            'body'         => $this->submitterName . ' wuxuu kuu diray dhaqdhaqaaqa dacwadda ' . $this->fileNo . ' si aad u aqbasho.',
            'handover_id'  => $this->handover->id,
            'file_no'      => $this->fileNo,
            'link'         => route('criminal-case-handover.approval'),
        ];
    }
}
