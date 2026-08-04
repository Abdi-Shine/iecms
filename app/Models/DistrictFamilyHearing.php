<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyHearing extends Model
{
    protected $table   = 'district_family_hearings';
    protected $guarded = [];

    protected $casts = [
        'hearing_date' => 'date',
    ];

    public function familyCase()
    {
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID')->with('court');
    }
}
