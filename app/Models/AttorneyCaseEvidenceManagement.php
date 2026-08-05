<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseEvidenceManagement extends Model
{
    protected $table = 'attorney_case_evidence_managements';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_collected' => 'date',
            'catalogued'     => 'boolean',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
