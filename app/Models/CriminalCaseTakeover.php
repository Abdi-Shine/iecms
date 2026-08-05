<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCaseTakeover extends Model
{
    use Auditable;

    protected $table   = 'criminal_case_takeovers';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'outgoing_acknowledged_at' => 'datetime',
            'incoming_accepted_at'     => 'datetime',
            'admin_approved_at'        => 'datetime',
            'rejected_at'              => 'datetime',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function outgoingInvestigator()
    {
        return $this->belongsTo(User::class, 'outgoing_investigator_id');
    }

    public function incomingInvestigator()
    {
        return $this->belongsTo(User::class, 'incoming_investigator_id');
    }

    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    public function statusLabel(): string
    {
        if ($this->rejected_at) return 'Rejected';
        if ($this->admin_approved_at) return 'Approved';
        if ($this->incoming_accepted_at) return 'Awaiting Admin Approval';
        if ($this->outgoing_acknowledged_at) return 'Awaiting Incoming Acceptance';
        return 'Awaiting Outgoing Acknowledgment';
    }
}
