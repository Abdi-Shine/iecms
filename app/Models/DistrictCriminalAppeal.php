<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalAppeal extends Model
{
    protected $table    = 'district_criminal_appeals';
    protected $fillable = [
        'criminal_case_id', 'appeal_type', 'appeal_date',
        'appealing_parties', 'additional_notes', 'attachment', 'status', 'created_by',
    ];

    protected $casts = [
        'appeal_date'       => 'date',
        'appealing_parties' => 'array',
    ];

    public function criminalCase()
    {
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID');
    }
}
