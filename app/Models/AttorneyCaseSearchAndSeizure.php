<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseSearchAndSeizure extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'application_date'       => 'date',
            'search_conducted_date'  => 'date',
            'property_receipt_issued' => 'boolean',
            'approved_date'          => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
