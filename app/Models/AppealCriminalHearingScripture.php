<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalHearingScripture extends Model
{
    protected $table   = 'appeal_criminal_hearing_scriptures';
    protected $guarded = [];

    public function criminalCase()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
    }

    public function hearing()
    {
        return $this->belongsTo(AppealCriminalHearing::class);
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
