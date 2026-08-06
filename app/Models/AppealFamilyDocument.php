<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyDocument extends Model
{
    protected $table      = 'appeal_family_documents';
    protected $primaryKey = 'DID';
    protected $guarded    = [];

    public function familyCase()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }
}
