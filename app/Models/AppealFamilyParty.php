<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyParty extends Model
{
    protected $table      = 'appeal_family_parties';
    protected $primaryKey = 'PID';
    protected $guarded    = [];

    protected function casts(): array
    {
        return [
            'national_id'      => 'encrypted',
            'passport_number'  => 'encrypted',
        ];
    }

    public function familyCase()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }
}
