<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilDocument extends Model
{
    protected $table      = 'appeal_civil_documents';
    protected $primaryKey = 'DID';
    protected $guarded    = [];

    public function civilCase()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }
}
