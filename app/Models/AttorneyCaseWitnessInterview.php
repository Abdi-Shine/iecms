<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseWitnessInterview extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'interview_date'   => 'date',
            'follow_up_needed' => 'boolean',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
