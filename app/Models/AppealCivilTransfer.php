<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilTransfer extends Model
{
    protected $table    = 'appeal_civil_transfers';
    protected $fillable = [
        'civil_case_id', 'from_court', 'to_court',
        'transfer_date', 'reason', 'notes', 'attachment', 'status', 'created_by',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function civilCase()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
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
