<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriminalCaseEvidenceCustodyLog extends Model
{
    protected $table   = 'criminal_case_evidence_custody_logs';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'transferred_at' => 'datetime',
        ];
    }

    public function evidenceItem()
    {
        return $this->belongsTo(CriminalCaseEvidenceItem::class, 'evidence_item_id');
    }
}
