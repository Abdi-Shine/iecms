<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilAppeal extends Model
{
    protected $table    = 'appeal_civil_appeals';
    protected $fillable = [
        'civil_case_id', 'appeal_type', 'appeal_date',
        'appealing_parties', 'additional_notes', 'attachment', 'status', 'created_by',
    ];

    protected $casts = [
        'appeal_date'       => 'date',
        'appealing_parties' => 'array',
    ];

    public function civilCase()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }
}
