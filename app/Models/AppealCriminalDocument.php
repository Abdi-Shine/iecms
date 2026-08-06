<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalDocument extends Model
{
    protected $table      = 'appeal_criminal_documents';
    protected $primaryKey = 'DID';
    protected $guarded    = [];

    public function criminalCase()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
    }
}
