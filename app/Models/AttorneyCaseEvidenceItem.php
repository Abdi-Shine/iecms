<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseEvidenceItem extends Model
{
    protected $table   = 'attorney_case_evidence_items';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'collected_date' => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
