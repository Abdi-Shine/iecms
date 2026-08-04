<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Judgment extends Model
{
    protected $table   = 'district_civil_judgments';
    protected $guarded = [];

    public function civilCase()
    {
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }

    public function receipts()
    {
        return $this->hasMany(JudgmentReceipt::class, 'judgment_id');
    }

    protected $casts = [];

    protected static function booted(): void
    {
        static::creating(function ($judgment) {
            $next = static::max('id') + 1;
            $judgment->form_id = 'JDG-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$judgment->created_by) {
                $judgment->created_by = auth()->user()->name ?? null;
            }
        });
    }
}
