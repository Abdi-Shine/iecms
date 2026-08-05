<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriminalBulkArrestMember extends Model
{
    protected $table   = 'criminal_bulk_arrest_members';
    protected $guarded = [];

    public function event()
    {
        return $this->belongsTo(CriminalBulkArrestEvent::class, 'bulk_arrest_event_id');
    }

    public function assignedInvestigator()
    {
        return $this->belongsTo(User::class, 'assigned_investigator_id');
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }
}
