<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CivilCaseCloseCase extends Model
{
    protected $table    = 'district_civil_close';
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
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }
}
