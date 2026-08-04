<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilJudgmentReceipt extends Model
{
    protected $table    = 'appeal_civil_judgment_receipts';
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
        return $this->belongsTo(AppealCivilJudgment::class, 'judgment_id');
    }

    public function party()
    {
        return $this->belongsTo(AppealCivilParty::class, 'party_id', 'PID');
    }
}
