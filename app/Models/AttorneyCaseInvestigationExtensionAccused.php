<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseInvestigationExtensionAccused extends Model
{
    protected $table = 'attorney_case_investigation_extension_accused';

    protected $guarded = [];

    public function investigationExtension()
    {
        return $this->belongsTo(AttorneyCaseInvestigationExtension::class, 'investigation_extension_id');
    }
}
