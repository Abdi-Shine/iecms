<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriminalCaseAssignment extends Model
{
    protected $table   = 'criminal_case_assignments';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'investigation_start_date' => 'date',
            'target_completion_date'   => 'date',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function assignedInvestigator()
    {
        return $this->belongsTo(User::class, 'assigned_investigator_id');
    }
}
