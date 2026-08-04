<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionHearingScripture extends Model
{
    protected $table   = 'district_execution_hearing_scriptures';
    protected $guarded = [];

    public function executionCase()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }

    public function hearing()
    {
        return $this->belongsTo(DistrictExecutionHearing::class, 'hearing_id');
    }

    protected static function booted(): void
    {
        static::creating(function ($scripture) {
            $next = static::max('id') + 1;
            $scripture->form_id = 'BCME-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$scripture->created_by) {
                $scripture->created_by = auth()->user()->name ?? null;
            }
        });
    }
}
