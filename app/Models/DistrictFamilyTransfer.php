<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyTransfer extends Model
{
    protected $table    = 'district_family_transfers';
    protected $fillable = [
        'family_case_id', 'from_court', 'to_court',
        'transfer_date', 'reason', 'notes', 'attachment', 'status', 'created_by',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function familyCase()
    {
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID');
    }

    public function fromCourt()
    {
        return $this->belongsTo(Court::class, 'from_court', 'courtcode');
    }

    public function toCourt()
    {
        return $this->belongsTo(Court::class, 'to_court', 'courtcode');
    }
}
