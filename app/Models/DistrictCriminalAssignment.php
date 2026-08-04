<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalAssignment extends Model
{
    protected $table    = 'district_criminal_assignments';
    protected $fillable = [
        'criminal_case_id', 'employee_id', 'panel_role', 'assigned_by',
        'assignment_date', 'status', 'notes'
    ];

    public function case()
    {
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'AID');
    }

    /** Always read status from the parent case — never from the local column. */
    public function getStatusAttribute(): string
    {
        return $this->case?->Status ?? $this->attributes['status'] ?? '—';
    }

    public function getCaseStatusAttribute(): string
    {
        return $this->getStatusAttribute();
    }
}
