<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionEnforcement extends Model
{
    protected $table    = 'district_execution_enforcements';
    protected $fillable = [
        'execution_case_id',
        'enforcement_type',
        'enforcement_date',
        'description',
        'orders',
        'additional_notes',
        'attachment',
        'status',
        'created_by',
    ];

    protected $casts = [
        'enforcement_date' => 'date',
    ];

    public function case()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }
}
