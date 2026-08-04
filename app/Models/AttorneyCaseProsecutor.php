<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseProsecutor extends Model
{
    protected $table   = 'attorney_case_prosecutors';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function prosecutor()
    {
        return $this->belongsTo(Employee::class, 'prosecutor_id', 'AID');
    }
}
