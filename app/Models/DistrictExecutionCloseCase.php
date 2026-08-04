<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionCloseCase extends Model
{
    protected $table    = 'district_execution_close';
    protected $fillable = [
        'execution_case_id',
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
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }
}
