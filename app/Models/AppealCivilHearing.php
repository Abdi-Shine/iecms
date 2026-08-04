<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilHearing extends Model
{
    protected $table   = 'appeal_civil_hearings';
    protected $guarded = [];

    protected $casts = ['hearing_date' => 'date'];

    public function civilCase()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID')->with('court');
    }
}
