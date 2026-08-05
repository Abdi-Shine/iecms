<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseSuspectInterview extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'interview_date'        => 'date',
            'interpreter_used'      => 'boolean',
            'legal_counsel_present' => 'boolean',
            'rights_informed'       => 'boolean',
            'statement_voluntary'   => 'boolean',
            'signature_obtained'    => 'boolean',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
