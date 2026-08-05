<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCourtAppearanceForm extends Model
{
    use Auditable;

    protected $table   = 'criminal_court_appearance_forms';
    protected $guarded = [];

    public const TYPES = [
        'witness_summons'  => 'Witness Summons',
        'officer_subpoena' => 'Officer Subpoena',
        'production_order' => 'Production Order',
        'attendance_form'  => 'Attendance Form',
    ];

    public const STATUSES = ['Generated', 'Served', 'Acknowledged', 'Defaulted'];

    protected function casts(): array
    {
        return [
            'served_date' => 'date',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function courtAppearance()
    {
        return $this->belongsTo(CriminalCaseCourtAppearance::class, 'court_appearance_id');
    }
}
