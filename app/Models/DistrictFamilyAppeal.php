<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyAppeal extends Model
{
    protected $table    = 'district_family_appeals';
    protected $fillable = [
        'family_case_id', 'appeal_type', 'appeal_date',
        'appealing_parties', 'additional_notes', 'attachment', 'status', 'created_by',
    ];

    protected $casts = [
        'appeal_date'       => 'date',
        'appealing_parties' => 'array',
    ];

    public function familyCase()
    {
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID');
    }
}
