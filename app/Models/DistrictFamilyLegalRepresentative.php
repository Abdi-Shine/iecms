<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyLegalRepresentative extends Model
{
    protected $table    = 'district_family_legal_representatives';
    protected $guarded  = [];

    public function familyCase()
    {
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID');
    }

    public function party()
    {
        return $this->belongsTo(DistrictFamilyParty::class, 'party_id', 'PID');
    }
}
