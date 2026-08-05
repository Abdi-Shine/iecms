<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseArrestWithoutWarrant extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'arrest_date'     => 'date',
            'report_date'     => 'date',
            'force_used'      => 'boolean',
            'rights_informed' => 'boolean',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
