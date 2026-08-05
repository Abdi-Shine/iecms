<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseWarrantOfArrest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'application_date' => 'date',
            'issue_date'       => 'date',
            'expiry_date'      => 'date',
            'approved_date'    => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
