<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyLawyer extends Model
{
    protected $table    = 'district_family_lawyers';
    protected $fillable = [
        'family_case_id',
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
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function party()
    {
        return $this->belongsTo(DistrictFamilyParty::class, 'party_id', 'PID');
    }
}
