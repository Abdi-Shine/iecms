<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class AttorneyCase extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table      = 'attorney_cases';
    protected $primaryKey = 'ACID';
    protected $guarded    = [];

    public const CASE_NUMBER_PREFIX = 'XXIG';

    protected static function booted(): void
    {
        static::creating(function ($case) {
            if (!$case->case_number) {
                $case->case_number = static::nextCaseNumber($case->source_of_case);
            }
            if (!$case->status) {
                $case->status = 'Diiwaan';
            }
        });
    }

    /**
     * The case-number group code for a given "Isha Dacwadda" (source of case)
     * selection. Each group gets its own independent serial sequence.
     */
    public static function caseNumberGroup(?string $sourceOfCase): string
    {
        return match ($sourceOfCase) {
            "Hay'ad Dawladeed", 'Government Agency' => 'HD',
            'Baaris Xig' => 'BXIG',
            default => 'WDD',
        };
    }

    /**
     * Next sequential case number in the XXIG/{group}/{serial}/{year} format,
     * resetting the serial each year per group (mirrors the district registries'
     * FileNo generation pattern).
     */
    public static function nextCaseNumber(?string $sourceOfCase = null): string
    {
        $group    = static::caseNumberGroup($sourceOfCase);
        $prefix   = static::CASE_NUMBER_PREFIX . '/' . $group;
        $currYear = date('Y');

        // Case numbers must stay globally unique across institutions, not
        // just within the current user's institution.
        $last = static::withoutGlobalScopes()
            ->where('case_number', 'like', "{$prefix}/%/{$currYear}")
            ->orderByDesc('ACID')
            ->value('case_number');

        $serial = 1;
        if ($last) {
            $parts = explode('/', $last);
            if (count($parts) >= 2) {
                $serial = intval($parts[count($parts) - 2]) + 1;
            }
        }

        return sprintf('%s/%d/%s', $prefix, $serial, $currYear);
    }

    /**
     * Preview of the next case number for each group, used to show a live
     * preview in the intake wizard before the source of case is submitted.
     */
    public static function nextCaseNumberPreviewMap(): array
    {
        return [
            'WDD'  => static::nextCaseNumber(null),
            'HD'   => static::nextCaseNumber("Hay'ad Dawladeed"),
            'BXIG' => static::nextCaseNumber('Baaris Xig'),
        ];
    }

    public function parties()
    {
        return $this->hasMany(AttorneyCaseParty::class, 'attorney_case_id', 'ACID');
    }

    public function investigation()
    {
        return $this->hasOne(AttorneyInvestigation::class, 'attorney_case_id', 'ACID');
    }

    public function investigationDecision()
    {
        return $this->hasOne(AttorneyCaseInvestigationDecision::class, 'attorney_case_id', 'ACID');
    }

    public function complianceForms()
    {
        return $this->hasMany(AttorneyComplianceForm::class, 'attorney_case_id', 'ACID');
    }

    public function prosecutorAssignments()
    {
        return $this->hasMany(AttorneyCaseProsecutor::class, 'attorney_case_id', 'ACID');
    }

    public function proceedings()
    {
        return $this->hasMany(AttorneyProceeding::class, 'attorney_case_id', 'ACID');
    }

    public function evidence()
    {
        return $this->hasMany(AttorneyEvidence::class, 'attorney_case_id', 'ACID');
    }

    public function documents()
    {
        return $this->hasMany(AttorneyCaseDocument::class, 'attorney_case_id', 'ACID');
    }

    public function activities()
    {
        return $this->hasMany(AttorneyCaseActivity::class, 'attorney_case_id', 'ACID')->orderBy('created_at');
    }

    public function reviews()
    {
        return $this->hasMany(AttorneyCaseReview::class, 'attorney_case_id', 'ACID')->orderByDesc('review_date')->orderByDesc('id');
    }

    public function latestReview()
    {
        return $this->hasOne(AttorneyCaseReview::class, 'attorney_case_id', 'ACID')->orderByDesc('review_date')->orderByDesc('id');
    }

    public function complainants()
    {
        return $this->hasMany(AttorneyCaseComplainant::class, 'attorney_case_id', 'ACID');
    }

    public function victims()
    {
        return $this->hasMany(AttorneyCaseVictim::class, 'attorney_case_id', 'ACID');
    }

    public function accused()
    {
        return $this->hasMany(AttorneyCaseAccused::class, 'attorney_case_id', 'ACID');
    }

    public function evidenceItems()
    {
        return $this->hasMany(AttorneyCaseEvidenceItem::class, 'attorney_case_id', 'ACID');
    }

    public function witnesses()
    {
        return $this->hasMany(AttorneyCaseWitness::class, 'attorney_case_id', 'ACID');
    }

    public function legalProvisions()
    {
        return $this->hasMany(AttorneyCaseLegalProvision::class, 'attorney_case_id', 'ACID');
    }
}
