<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriminalCaseOccurrenceBook extends Model
{
    protected $table   = 'criminal_case_occurrence_books';
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function ($ob) {
            if (!$ob->ob_number) {
                $institutionId = CriminalCase::withoutGlobalScopes()->where('id', $ob->criminal_case_id)->value('institution_id');
                $ob->ob_number = static::nextObNumber($institutionId);
            }
        });
    }

    public static function nextObNumber(?int $institutionId = null): string
    {
        $config = CriminalNumberFormat::configFor('ob_number', $institutionId);
        $searchPrefix = CriminalNumberFormat::searchPrefix($config);

        $last = static::where('ob_number', 'like', $searchPrefix . '%')
            ->orderByDesc('id')
            ->value('ob_number');

        $serial = 1;
        if ($last) {
            $serial = intval(substr($last, strlen($searchPrefix))) + 1;
        }

        CriminalNumberFormat::markUsed('ob_number', $institutionId);

        return CriminalNumberFormat::format($config, $serial);
    }

    protected function casts(): array
    {
        return [
            'complainant_id'              => 'encrypted',
            'ob_datetime'                 => 'datetime',
            'incident_datetime'           => 'datetime',
            'supervisor_acknowledged_at'  => 'datetime',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function assignedInvestigator()
    {
        return $this->belongsTo(User::class, 'assigned_investigator_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_acknowledged_by');
    }

    public function isComplete(): bool
    {
        return $this->exists
            && $this->assigned_investigator_id !== null
            && $this->supervisor_acknowledged_at !== null;
    }

    /**
     * Derived, not stored — avoids a status column drifting out of sync
     * with the case/assignment/acknowledgment state it's computed from.
     */
    public function statusLabel(): string
    {
        if ($this->criminalCase?->status === 'Closed') {
            return 'Closed';
        }
        if ($this->isComplete()) {
            return 'Active';
        }
        if ($this->assigned_investigator_id !== null) {
            return 'Assigned';
        }
        return 'Draft';
    }
}
