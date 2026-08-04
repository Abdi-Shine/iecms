<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseParty extends Model
{
    protected $table   = 'attorney_case_parties';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'national_id' => 'encrypted',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
