<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalParty extends Model
{
    protected $table      = 'appeal_criminal_parties';
    protected $primaryKey = 'PID';
    protected $guarded    = [];

    protected function casts(): array
    {
        return [
            'national_id'      => 'encrypted',
            'passport_number'  => 'encrypted',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
    }
}
