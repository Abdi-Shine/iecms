<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalJudgmentReceipt extends Model
{
    protected $table    = 'district_criminal_judgment_receipts';
    protected $fillable = [
        'judgment_id', 'party_id', 'party_name', 'party_role',
        'judgment_outcome', 'received_date', 'signature',
        'rep_name', 'rep_signature',
        'received_at', 'received_by', 'notes',
    ];

    protected $casts = [
        'received_at'   => 'datetime',
        'received_date' => 'date',
    ];

    public function judgment()
    {
        return $this->belongsTo(DistrictCriminalJudgment::class, 'judgment_id');
    }

    public function party()
    {
        return $this->belongsTo(DistrictCriminalParty::class, 'party_id', 'PID');
    }
}
