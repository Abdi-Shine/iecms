<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilLawyer extends Model
{
    protected $table    = 'appeal_civil_lawyers';
    protected $fillable = [
        'civil_case_id',
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
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function party()
    {
        return $this->belongsTo(AppealCivilParty::class, 'party_id', 'PID');
    }
}
