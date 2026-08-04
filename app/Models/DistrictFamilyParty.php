<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyParty extends Model
{
    protected $table = 'district_family_parties';
    protected $primaryKey = 'PID';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'national_id'     => 'encrypted',
            'passport_number' => 'encrypted',
        ];
    }

    public function familyCase()
    {
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID');
    }
}
