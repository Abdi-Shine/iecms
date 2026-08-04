<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyProceeding extends Model
{
    protected $table   = 'attorney_proceedings';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'hearing_date' => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id', 'CAI');
    }

    public function judge()
    {
        return $this->belongsTo(Employee::class, 'judge_id', 'AID');
    }
}
