<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionReturnFile extends Model
{
    protected $table    = 'district_execution_return_files';
    protected $fillable = [
        'execution_case_id', 'documents', 'special_instructions', 'additional_notes', 'status', 'created_by',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function case()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID')->with('court');
    }
}
