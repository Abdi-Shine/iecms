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

    protected static function booted(): void
    {
        static::creating(function ($item) {
            if (!$item->evidence_id) {
                $institutionId = CriminalCase::withoutGlobalScopes()->where('id', $item->criminal_case_id)->value('institution_id');
                $item->evidence_id = static::nextEvidenceId($institutionId);
            }
        });
    }

    public static function nextEvidenceId(?int $institutionId = null): string
    {
        $config = CriminalNumberFormat::configFor('evidence_id', $institutionId);
        $searchPrefix = CriminalNumberFormat::searchPrefix($config);

        $last = static::where('evidence_id', 'like', $searchPrefix . '%')
            ->orderByDesc('id')
            ->value('evidence_id');

        $serial = 1;
        if ($last) {
            $serial = intval(substr($last, strlen($searchPrefix))) + 1;
        }

        CriminalNumberFormat::markUsed('evidence_id', $institutionId);

        return CriminalNumberFormat::format($config, $serial);
    }

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
