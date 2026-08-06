<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalEnforcement extends Model
{
    protected $table    = 'appeal_criminal_enforcements';
    protected $fillable = [
        'criminal_case_id',
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
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
    }
}
