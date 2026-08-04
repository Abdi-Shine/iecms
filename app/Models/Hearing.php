<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
    protected $table   = 'district_civil_hearings';
    protected $guarded = [];

    protected $casts = [
        'hearing_date' => 'date',
    ];

    public function civilCase()
    {
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID')->with('court');
    }
}
