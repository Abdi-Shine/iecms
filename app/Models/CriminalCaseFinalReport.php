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
                $report->report_number = static::nextReportNumber();
            }
        });
    }

    public static function nextReportNumber(): string
    {
        $prefix = 'RPT-CID-' . date('Y') . '-';

        $last = static::where('report_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('report_number');

        $serial = 1;
        if ($last) {
            $serial = intval(substr($last, strlen($prefix))) + 1;
        }

        return $prefix . str_pad($serial, 5, '0', STR_PAD_LEFT);
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
