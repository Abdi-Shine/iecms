<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalHearing extends Model
{
    protected $table   = 'appeal_criminal_hearings';
    protected $guarded = [];

    protected $casts = ['hearing_date' => 'date'];

    public function criminalCase()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID')->with('court');
    }
}
