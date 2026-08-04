<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyComplianceForm extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'signed_date' => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'AID');
    }
}
