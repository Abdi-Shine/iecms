<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCaseBiometric extends Model
{
    use Auditable;

    protected $table   = 'criminal_case_biometrics';
    protected $guarded = [];

    public const MATCH_STATUSES = ['Not Checked', 'Match Found', 'No Match', 'Inconclusive'];

    protected function casts(): array
    {
        return [
            'captured_date' => 'date',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }
}
