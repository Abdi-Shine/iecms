<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyHearing extends Model
{
    protected $table   = 'appeal_family_hearings';
    protected $guarded = [];

    protected $casts = ['hearing_date' => 'date'];

    public function familyCase()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID')->with('court');
    }
}
