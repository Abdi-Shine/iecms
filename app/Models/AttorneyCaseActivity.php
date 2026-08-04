<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseActivity extends Model
{
    protected $table   = 'attorney_case_activities';
    protected $guarded = [];

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
