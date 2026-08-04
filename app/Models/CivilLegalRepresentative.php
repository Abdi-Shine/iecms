<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CivilLegalRepresentative extends Model
{
    protected $table    = 'district_civil_legal_representatives';
    protected $guarded  = [];

    public function civilCase()
    {
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }

    public function party()
    {
        return $this->belongsTo(CivilCaseParty::class, 'party_id', 'PID');
    }
}
