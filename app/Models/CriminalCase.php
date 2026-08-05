<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class CriminalCase extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table   = 'criminal_cases';
    protected $guarded = [];

    public const STAGES = [
        'arrest',
        'occurrence_book',
        'case_assignment_evidence',
        'custody_court_scheduling',
        'final_report_ago_submission',
        'closed',
    ];

    protected static function booted(): void
    {
        static::creating(function ($case) {
            if (!$case->case_number) {
                $case->case_number = static::nextCaseNumber();
            }
        });
    }

    public static function nextCaseNumber(): string
    {
        $prefix = 'CASE-CID-' . date('Y') . '-';

        $last = static::withoutGlobalScopes()
            ->where('case_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('case_number');

        $serial = 1;
        if ($last) {
            $serial = intval(substr($last, strlen($prefix))) + 1;
        }

        return $prefix . str_pad($serial, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Advance to the given stage if it's further along than the case's
     * current stage. Never moves backwards through STAGES.
     */
    public function advanceStageTo(string $stage): void
    {
        if (array_search($stage, self::STAGES) > array_search($this->current_stage, self::STAGES)) {
            $this->update(['current_stage' => $stage]);
        }
    }

    public function arrest()
    {
        return $this->hasOne(CriminalCaseArrest::class);
    }

    public function occurrenceBook()
    {
        return $this->hasOne(CriminalCaseOccurrenceBook::class);
    }

    public function assignment()
    {
        return $this->hasOne(CriminalCaseAssignment::class);
    }

    public function evidenceItems()
    {
        return $this->hasMany(CriminalCaseEvidenceItem::class)->orderByDesc('created_at');
    }

    public function custody()
    {
        return $this->hasOne(CriminalCaseCustody::class);
    }

    public function courtAppearances()
    {
        return $this->hasMany(CriminalCaseCourtAppearance::class)->orderByDesc('appearance_date');
    }

    public function finalReport()
    {
        return $this->hasOne(CriminalCaseFinalReport::class);
    }
}
