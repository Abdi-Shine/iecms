<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyInvestigationUpdate extends Model
{
    protected $table   = 'attorney_investigation_updates';
    protected $guarded = [];

    public function investigation()
    {
        return $this->belongsTo(AttorneyInvestigation::class, 'attorney_investigation_id');
    }
}
