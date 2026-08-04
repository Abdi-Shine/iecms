<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilEnforcement extends Model
{
    protected $table    = 'appeal_civil_enforcements';
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
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }
}
