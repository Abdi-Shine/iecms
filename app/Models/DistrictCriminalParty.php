<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalParty extends Model
{
    protected $table = 'district_criminal_parties';
    protected $primaryKey = 'PID';
    protected $guarded = [];

    public function criminalCase()
    {
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID');
    }
}
