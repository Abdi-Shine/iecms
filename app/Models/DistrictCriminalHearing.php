<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalHearing extends Model
{
    protected $table   = 'district_criminal_hearings';
    protected $guarded = [];

    protected $casts = [
        'hearing_date' => 'date',
    ];

    public function criminalCase()
    {
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID')->with('court');
    }
}
