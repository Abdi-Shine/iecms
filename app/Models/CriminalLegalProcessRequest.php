<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalLegalProcessRequest extends Model
{
    use Auditable;

    protected $table   = 'criminal_legal_process_requests';
    protected $guarded = [];

    public const TYPES = [
        'warrant_of_arrest_ago'      => 'Warrant of Arrest (AGO)',
        'search_seizure_ago'         => 'Search & Seizure (AGO)',
        'asset_recovery_ago'         => 'Asset Recovery (AGO)',
        'arrest_without_warrant_ago' => 'Arrest Without Warrant (AGO)',
        'search_warrant_court'       => 'Search Warrant (Court)',
    ];

    public const AGO_TYPES = ['warrant_of_arrest_ago', 'search_seizure_ago', 'asset_recovery_ago', 'arrest_without_warrant_ago'];

    public const STATUSES = ['Requested', 'Ratified', 'Issued', 'Executed', 'Expired', 'Cancelled', 'Challenged', 'Declined'];

    protected function casts(): array
    {
        return [
            'issue_date'      => 'date',
            'expiry_date'     => 'date',
            'execution_date'  => 'date',
            'estimated_value' => 'decimal:2',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function isAgoRouted(): bool
    {
        return in_array($this->request_type, self::AGO_TYPES);
    }

    public function isOverdueForRatification(): bool
    {
        return $this->request_type === 'arrest_without_warrant_ago'
            && $this->status === 'Requested'
            && $this->created_at->lt(now()->subHours(48));
    }
}
