<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseAssetRecovery extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'application_date' => 'date',
            'seizure_date'     => 'date',
            'approved_date'    => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
