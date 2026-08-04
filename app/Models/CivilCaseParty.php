<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CivilCaseParty extends Model
{
    protected $table = 'district_civil_parties';
    protected $primaryKey = 'PID';
    protected $guarded = [];

    public function civilCase()
    {
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }
}
