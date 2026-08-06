<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyEnforcement extends Model
{
    protected $table    = 'appeal_family_enforcements';
    protected $fillable = [
        'family_case_id',
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
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }
}
