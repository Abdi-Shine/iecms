<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseAccused extends Model
{
    protected $table   = 'attorney_case_accused';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_of_birth'         => 'date',
            'id_number'             => 'encrypted',
            'custodian_id_number'   => 'encrypted',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
