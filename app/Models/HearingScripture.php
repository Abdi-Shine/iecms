<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HearingScripture extends Model
{
    protected $table   = 'district_civil_hearing_scriptures';
    protected $guarded = [];

    public function civilCase()
    {
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }

    public function hearing()
    {
        return $this->belongsTo(Hearing::class);
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
