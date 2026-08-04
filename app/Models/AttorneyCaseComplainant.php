<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseComplainant extends Model
{
    protected $table   = 'attorney_case_complainants';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
