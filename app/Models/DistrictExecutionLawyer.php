<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionLawyer extends Model
{
    protected $table    = 'district_execution_lawyers';
    protected $fillable = [
        'execution_case_id',
        'lawyer_id',
        'party_id',
        'party_role',
        'lawyer_type',
        'assignment_date',
        'reason',
        'status',
        'addedBy',
        'addedDate'
    ];

    public function case()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function party()
    {
        return $this->belongsTo(DistrictExecutionParty::class, 'party_id', 'PID');
    }
}
