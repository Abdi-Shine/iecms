<?php

namespace App\Notifications;

use App\Models\DistrictExecutionHandover;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExecutionHandoverSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public DistrictExecutionHandover $handover,
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
            'type'         => 'execution_handover_submitted',
            'title'        => 'Dhaqdhaqaaqa Cusub — ' . $this->fileNo,
            'body'         => $this->submitterName . ' wuxuu kuu diray dhaqdhaqaaqa dacwadda ' . $this->fileNo . ' si aad u aqbasho.',
            'handover_id'  => $this->handover->id,
            'file_no'      => $this->fileNo,
            'link'         => route('execution-case-handover.approval'),
        ];
    }
}
