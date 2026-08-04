<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilCloseCase extends Model
{
    protected $table    = 'appeal_civil_close';
    protected $fillable = [
        'civil_case_id',
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
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }
}
