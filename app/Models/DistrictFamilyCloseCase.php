<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyCloseCase extends Model
{
    protected $table    = 'district_family_close';
    protected $fillable = [
        'family_case_id',
        'judgment_type',
        'judgment_date',
        'decision_body',
        'status',
        'created_by',
    ];

    protected $casts = [
        'judgment_date' => 'date',
    ];

    public function case()
    {
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID');
    }
}
