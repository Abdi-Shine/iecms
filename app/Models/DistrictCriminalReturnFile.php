<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalReturnFile extends Model
{
    protected $table    = 'district_criminal_return_files';
    protected $fillable = [
        'criminal_case_id', 'documents', 'special_instructions', 'additional_notes', 'status', 'created_by',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function case()
    {
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID')->with('court');
    }
}
