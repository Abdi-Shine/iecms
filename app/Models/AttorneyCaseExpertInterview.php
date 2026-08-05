<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseExpertInterview extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'interview_date'   => 'date',
            'report_attached'  => 'boolean',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
