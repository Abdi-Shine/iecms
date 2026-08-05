<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCaseInvestigationReport extends Model
{
    use Auditable;

    protected $table   = 'criminal_case_investigation_reports';
    protected $guarded = [];

    public const TYPES = ['Progress Report', 'Interim Report', 'Expert-Forensic Report'];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['Approved', 'Submitted']);
    }
}
