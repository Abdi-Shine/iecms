<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseVictimInterview extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'interview_date'              => 'date',
            'support_person_present'      => 'boolean',
            'medical_treatment_required'  => 'boolean',
            'protective_measures_needed'  => 'boolean',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
