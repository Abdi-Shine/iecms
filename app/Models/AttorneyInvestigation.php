<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyInvestigation extends Model
{
    protected $table   = 'attorney_investigations';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date'      => 'date',
            'end_date'        => 'date',
            'signature_date'  => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function investigator()
    {
        return $this->belongsTo(Employee::class, 'investigator_id', 'AID');
    }

    public function updates()
    {
        return $this->hasMany(AttorneyInvestigationUpdate::class, 'attorney_investigation_id')->orderBy('created_at');
    }
}
