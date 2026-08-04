<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CivilCaseEnforcement extends Model
{
    protected $table    = 'district_civil_enforcements';
    protected $fillable = [
        'civil_case_id',
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
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }
}
