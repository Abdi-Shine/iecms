<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseLegalProvision extends Model
{
    protected $guarded = [];

    public function case()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
