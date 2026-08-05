<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCaseCustody extends Model
{
    use Auditable;

    protected $table   = 'criminal_case_custodies';
    protected $guarded = [];

    public const STATUSES = ['In Custody', 'Released on Bail', 'Released', 'Transferred', 'Remanded'];

    protected function casts(): array
    {
        return [
            'custody_start_date'  => 'date',
            'legal_deadline'      => 'date',
            'custody_review_date' => 'date',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }
}
