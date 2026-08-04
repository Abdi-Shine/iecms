<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CivilCaseLawyer extends Model
{
    protected $table    = 'district_civil_lawyers';
    protected $fillable = [
        'civil_case_id',
        'lawyer_id',
        'party_id',
        'party_role',
        'assignment_date',
        'reason',
        'status',
        'addedBy',
        'addedDate'
    ];

    public function case()
    {
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function party()
    {
        return $this->belongsTo(CivilCaseParty::class, 'party_id', 'PID');
    }
}
