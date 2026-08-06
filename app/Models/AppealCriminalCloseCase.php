<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalCloseCase extends Model
{
    protected $table    = 'appeal_criminal_close';
    protected $fillable = [
        'criminal_case_id',
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
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
    }
}
