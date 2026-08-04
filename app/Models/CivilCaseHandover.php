<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CivilCaseHandover extends Model
{
    protected $table    = 'district_civil_handovers';
    protected $fillable = [
        'civil_case_id', 'documents', 'special_instructions', 'additional_notes', 'status', 'created_by',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function case()
    {
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID')->with('court');
    }
}
