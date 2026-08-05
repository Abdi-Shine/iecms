<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriminalCaseDiaryEntry extends Model
{
    protected $table   = 'criminal_case_diary_entries';
    protected $guarded = [];

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
