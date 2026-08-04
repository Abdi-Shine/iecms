<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionParty extends Model
{
    protected $table = 'district_execution_parties';
    protected $primaryKey = 'PID';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'national_id'     => 'encrypted',
            'passport_number' => 'encrypted',
        ];
    }

    public function executionCase()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }
}
