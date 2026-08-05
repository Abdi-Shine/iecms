<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class CriminalReceivedWarrant extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table   = 'criminal_received_warrants';
    protected $guarded = [];

    public const STATUSES = ['Received', 'Assigned', 'Executed', 'Unserved', 'Expired', 'Returned Unexecuted'];

    protected function casts(): array
    {
        return [
            'received_date'      => 'date',
            'warrant_expiry_date' => 'date',
        ];
    }

    public function assignedOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function isNearingExpiry(): bool
    {
        return $this->warrant_expiry_date
            && !in_array($this->execution_status, ['Executed', 'Returned Unexecuted', 'Expired'])
            && $this->warrant_expiry_date->lte(now()->addHours(72));
    }
}
