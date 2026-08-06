<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyAssignment extends Model
{
    protected $table    = 'appeal_family_assignments';
    protected $fillable = [
        'family_case_id', 'employee_id', 'panel_role', 'assigned_by',
        'assignment_date', 'status', 'notes',
    ];

    public function case()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'AID');
    }

    public function getStatusAttribute(): string
    {
        return $this->case?->Status ?? $this->attributes['status'] ?? '—';
    }

    public function getCaseStatusAttribute(): string
    {
        return $this->getStatusAttribute();
    }
}
