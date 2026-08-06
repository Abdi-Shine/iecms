<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyCloseCase extends Model
{
    protected $table    = 'appeal_family_close';
    protected $fillable = [
        'family_case_id',
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
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }
}
