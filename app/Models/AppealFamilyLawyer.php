<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyLawyer extends Model
{
    protected $table    = 'appeal_family_lawyers';
    protected $fillable = [
        'family_case_id',
        'lawyer_id',
        'party_id',
        'party_role',
        'assignment_date',
        'reason',
        'status',
        'addedBy',
        'addedDate',
    ];

    public function case()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function party()
    {
        return $this->belongsTo(AppealFamilyParty::class, 'party_id', 'PID');
    }
}
