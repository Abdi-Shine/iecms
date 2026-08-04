<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'civil_case_id', 'payer_name', 'payer_phone', 'payer_email', 'amount', 'currency', 'status',
        'payment_date', 'transaction_id', 'tariff_id',
        'court_id', 'cashier_id', 'approved_by', 'approved_at', 'notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id', 'CAI');
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class, 'tariff_id', 'id');
    }

    public function cashier()
    {
        return $this->belongsTo(Employee::class, 'cashier_id', 'AID');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'AID');
    }

    public function case()
    {
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }
}
