<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalLawyer extends Model
{
    protected $table    = 'district_criminal_lawyers';
    protected $fillable = [
        'criminal_case_id',
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
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function party()
    {
        return $this->belongsTo(DistrictCriminalParty::class, 'party_id', 'PID');
    }
}
