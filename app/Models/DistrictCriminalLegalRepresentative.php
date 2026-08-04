<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalLegalRepresentative extends Model
{
    protected $table    = 'district_criminal_legal_representatives';
    protected $guarded  = [];

    public function criminalCase()
    {
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID');
    }

    public function party()
    {
        return $this->belongsTo(DistrictCriminalParty::class, 'party_id', 'PID');
    }
}
