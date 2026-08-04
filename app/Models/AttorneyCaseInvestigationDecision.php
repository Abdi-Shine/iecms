<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseInvestigationDecision extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'decision_date' => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
