<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalHearingScripture extends Model
{
    protected $table   = 'district_criminal_hearing_scriptures';
    protected $guarded = [];

    public function criminalCase()
    {
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID');
    }

    public function hearing()
    {
        return $this->belongsTo(DistrictCriminalHearing::class, 'hearing_id');
    }

    protected static function booted(): void
    {
        static::creating(function ($scripture) {
            $next = static::max('id') + 1;
            $scripture->form_id = 'BCMC-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$scripture->created_by) {
                $scripture->created_by = auth()->user()->name ?? null;
            }
        });
    }
}
