<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionAppeal extends Model
{
    protected $table    = 'district_execution_appeals';
    protected $fillable = [
        'execution_case_id', 'appeal_type', 'appeal_date',
        'appealing_parties', 'additional_notes', 'attachment', 'status', 'created_by',
    ];

    protected $casts = [
        'appeal_date'       => 'date',
        'appealing_parties' => 'array',
    ];

    public function executionCase()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }
}
