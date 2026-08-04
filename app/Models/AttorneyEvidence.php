<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyEvidence extends Model
{
    protected $table   = 'attorney_evidence';
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
