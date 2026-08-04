<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilHearingScripture extends Model
{
    protected $table   = 'appeal_civil_hearing_scriptures';
    protected $guarded = [];

    public function civilCase()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }

    public function hearing()
    {
        return $this->belongsTo(AppealCivilHearing::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($scripture) {
            $next = static::max('id') + 1;
            $scripture->form_id = 'BCMS-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$scripture->created_by) {
                $scripture->created_by = auth()->user()->name ?? null;
            }
        });
    }
}
