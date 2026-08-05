<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseArrestDecision extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'decision_date'      => 'date',
            'flight_risk'        => 'boolean',
            'public_safety_risk' => 'boolean',
            'approved_date'      => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
