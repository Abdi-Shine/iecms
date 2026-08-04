<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionTransfer extends Model
{
    protected $table    = 'district_execution_transfers';
    protected $fillable = [
        'execution_case_id', 'from_court', 'to_court',
        'transfer_date', 'reason', 'notes', 'attachment', 'status', 'created_by',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function executionCase()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
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
