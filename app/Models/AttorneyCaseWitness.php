<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseWitness extends Model
{
    protected $table   = 'attorney_case_witnesses';
    protected $guarded = [];

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
