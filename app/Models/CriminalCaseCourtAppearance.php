<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriminalCaseCourtAppearance extends Model
{
    protected $table   = 'criminal_case_court_appearances';
    protected $guarded = [];

    public const HEARING_TYPES = ['Remand Hearing', 'Bail Application', 'Preliminary Inquiry', 'Trial'];

    protected function casts(): array
    {
        return [
            'appearance_date'  => 'date',
            'next_hearing_date' => 'date',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }
}
