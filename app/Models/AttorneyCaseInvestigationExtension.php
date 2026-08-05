<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseInvestigationExtension extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'current_deadline' => 'date',
            'new_deadline'     => 'date',
            'request_date'     => 'date',
            'approved_date'    => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
