<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilAssignment extends Model
{
    protected $table    = 'appeal_civil_assignments';
    protected $fillable = [
        'civil_case_id', 'employee_id', 'panel_role', 'assigned_by',
        'assignment_date', 'status', 'notes',
    ];

    public function case()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
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
