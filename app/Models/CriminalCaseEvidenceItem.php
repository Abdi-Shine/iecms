<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCaseEvidenceItem extends Model
{
    use Auditable;

    protected $table   = 'criminal_case_evidence_items';
    protected $guarded = [];

    public const STATUSES = ['collected', 'submitted_to_lab', 'lab_results_received', 'court_submitted', 'returned'];

    protected function casts(): array
    {
        return [
            'collection_date' => 'date',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function custodyLogs()
    {
        return $this->hasMany(CriminalCaseEvidenceCustodyLog::class, 'evidence_item_id')->orderByDesc('transferred_at');
    }
}
