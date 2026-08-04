<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilParty extends Model
{
    protected $table      = 'appeal_civil_parties';
    protected $primaryKey = 'PID';
    protected $guarded    = [];

    protected function casts(): array
    {
        return [
            'national_id'      => 'encrypted',
            'passport_number'  => 'encrypted',
        ];
    }

    public function civilCase()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }
}
