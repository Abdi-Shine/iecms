<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionHearing extends Model
{
    protected $table   = 'district_execution_hearings';
    protected $guarded = [];

    protected $casts = [
        'hearing_date' => 'date',
    ];

    public function executionCase()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID')->with('court');
    }
}
