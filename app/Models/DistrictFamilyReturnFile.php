<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyReturnFile extends Model
{
    protected $table    = 'district_family_return_files';
    protected $fillable = [
        'family_case_id', 'documents', 'special_instructions', 'additional_notes', 'status', 'created_by',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function case()
    {
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID')->with('court');
    }
}
