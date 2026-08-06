<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyLegalRepresentative extends Model
{
    protected $table   = 'appeal_family_legal_representatives';
    protected $guarded = [];

    public function familyCase()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }

    public function party()
    {
        return $this->belongsTo(AppealFamilyParty::class, 'party_id', 'PID');
    }
}
