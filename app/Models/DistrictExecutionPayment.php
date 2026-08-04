<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionPayment extends Model
{
    protected $table = 'district_execution_payments';
    protected $guarded = [];
    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function executionCase()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id', 'CAI');
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class, 'tariff_id');
    }

    public function cashier()
    {
        return $this->belongsTo(Employee::class, 'cashier_id', 'AID');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'AID');
    }
}
