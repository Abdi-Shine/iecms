<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCaseFinalReport extends Model
{
    use Auditable;

    protected $table   = 'criminal_case_final_reports';
    protected $guarded = [];

    public const RECOMMENDATIONS = [
        'Proceed to Prosecution',
        'Insufficient Evidence',
        'Refer for Further Investigation',
        'No Case to Answer',
    ];

    public const AGO_DECISIONS = ['Accept for Prosecution', 'Decline', 'Request More Info'];

    protected static function booted(): void
    {
        static::creating(function ($report) {
            if (!$report->report_number) {
                $institutionId = CriminalCase::withoutGlobalScopes()->where('id', $report->criminal_case_id)->value('institution_id');
                $report->report_number = static::nextReportNumber($institutionId);
            }
        });
    }

    public static function nextReportNumber(?int $institutionId = null): string
    {
        $config = CriminalNumberFormat::configFor('report_number', $institutionId);
        $searchPrefix = CriminalNumberFormat::searchPrefix($config);

        $last = static::where('report_number', 'like', $searchPrefix . '%')
            ->orderByDesc('id')
            ->value('report_number');

        $serial = 1;
        if ($last) {
            $serial = intval(substr($last, strlen($searchPrefix))) + 1;
        }

        CriminalNumberFormat::markUsed('report_number', $institutionId);

        return CriminalNumberFormat::format($config, $serial);
    }

    protected function casts(): array
    {
        return [
            'supervisor_endorsed_at' => 'datetime',
            'submitted_to_ago_at'    => 'datetime',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_endorsed_by');
    }

    public function agoReceivingOfficer()
    {
        return $this->belongsTo(User::class, 'ago_receiving_officer_id');
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_to_ago_at !== null;
    }
}
