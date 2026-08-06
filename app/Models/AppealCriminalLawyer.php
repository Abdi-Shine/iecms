<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalLawyer extends Model
{
    protected $table    = 'appeal_criminal_lawyers';
    protected $fillable = [
        'criminal_case_id',
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
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function party()
    {
        return $this->belongsTo(AppealCriminalParty::class, 'party_id', 'PID');
    }
}
