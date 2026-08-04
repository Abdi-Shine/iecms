<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionLegalRepresentative extends Model
{
    protected $table    = 'district_execution_legal_representatives';
    protected $guarded  = [];

    public function executionCase()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }

    public function party()
    {
        return $this->belongsTo(DistrictExecutionParty::class, 'party_id', 'PID');
    }
}
