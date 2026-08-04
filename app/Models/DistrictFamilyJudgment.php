<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyJudgment extends Model
{
    protected $table   = 'district_family_judgments';
    protected $guarded = [];

    public function familyCase()
    {
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID');
    }

    public function receipts()
    {
        return $this->hasMany(DistrictFamilyJudgmentReceipt::class, 'judgment_id');
    }

    protected $casts = [];

    protected static function booted(): void
    {
        static::creating(function ($judgment) {
            $next = static::max('id') + 1;
            $judgment->form_id = 'JDGF-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$judgment->created_by) {
                $judgment->created_by = auth()->user()->name ?? null;
            }
        });
    }
}
