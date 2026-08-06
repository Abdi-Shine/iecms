<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalTransfer extends Model
{
    protected $table    = 'appeal_criminal_transfers';
    protected $fillable = [
        'criminal_case_id', 'from_court', 'to_court',
        'transfer_date', 'reason', 'notes', 'attachment', 'status', 'created_by',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function criminalCase()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
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
