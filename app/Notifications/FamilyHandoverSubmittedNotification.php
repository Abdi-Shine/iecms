<?php

namespace App\Notifications;

use App\Models\DistrictFamilyHandover;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FamilyHandoverSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public DistrictFamilyHandover $handover,
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
            'type'         => 'family_handover_submitted',
            'title'        => 'Dhaqdhaqaaqa Cusub — ' . $this->fileNo,
            'body'         => $this->submitterName . ' wuxuu kuu diray dhaqdhaqaaqa dacwadda ' . $this->fileNo . ' si aad u aqbasho.',
            'handover_id'  => $this->handover->id,
            'file_no'      => $this->fileNo,
            'link'         => route('family-case-handover.approval'),
        ];
    }
}
