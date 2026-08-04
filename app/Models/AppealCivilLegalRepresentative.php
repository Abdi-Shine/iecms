<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilLegalRepresentative extends Model
{
    protected $table   = 'appeal_civil_legal_representatives';
    protected $guarded = [];

    public function civilCase()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }

    public function party()
    {
        return $this->belongsTo(AppealCivilParty::class, 'party_id', 'PID');
    }
}
