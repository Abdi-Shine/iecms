<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyHearingScripture extends Model
{
    protected $table   = 'appeal_family_hearing_scriptures';
    protected $guarded = [];

    public function familyCase()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }

    public function hearing()
    {
        return $this->belongsTo(AppealFamilyHearing::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($scripture) {
            $next = static::max('id') + 1;
            $scripture->form_id = 'BCMF-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$scripture->created_by) {
                $scripture->created_by = auth()->user()->name ?? null;
            }
        });
    }
}
