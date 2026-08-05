<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriminalCaseArrest extends Model
{
    protected $table   = 'criminal_case_arrests';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'arrestee_national_id' => 'encrypted',
            'arrestee_dob'         => 'date',
            'arrest_date'          => 'date',
            'warrant_issue_date'   => 'date',
            'warrant_expiry_date'  => 'date',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }
}
