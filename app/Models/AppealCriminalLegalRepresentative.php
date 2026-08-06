<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalLegalRepresentative extends Model
{
    protected $table   = 'appeal_criminal_legal_representatives';
    protected $guarded = [];

    public function criminalCase()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
    }

    public function party()
    {
        return $this->belongsTo(AppealCriminalParty::class, 'party_id', 'PID');
    }
}
