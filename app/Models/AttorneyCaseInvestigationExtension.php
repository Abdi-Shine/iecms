<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseInvestigationExtension extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_registered_investigation'          => 'date',
            'date_offence_occurred'                   => 'date',
            'date_investigation_commenced'             => 'date',
            'approved_date'                            => 'date',
            'reason_ongoing_investigation'              => 'boolean',
            'reason_awaiting_scan_results'               => 'boolean',
            'reason_awaiting_institutional_experts'      => 'boolean',
            'reason_awaiting_witness_statements'         => 'boolean',
            'reason_other'                                => 'boolean',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function accused()
    {
        return $this->hasMany(AttorneyCaseInvestigationExtensionAccused::class, 'investigation_extension_id');
    }
}
