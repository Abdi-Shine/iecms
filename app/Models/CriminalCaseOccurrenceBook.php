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
                $ob->ob_number = static::nextObNumber();
            }
        });
    }

    public static function nextObNumber(): string
    {
        $prefix = 'OB-CID-' . date('Y') . '-';

        $last = static::where('ob_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('ob_number');

        $serial = 1;
        if ($last) {
            $serial = intval(substr($last, strlen($prefix))) + 1;
        }

        return $prefix . str_pad($serial, 5, '0', STR_PAD_LEFT);
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
}
