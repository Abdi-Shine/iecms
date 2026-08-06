<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyJudgment extends Model
{
    protected $table   = 'appeal_family_judgments';
    protected $guarded = [];

    public function familyCase()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID');
    }

    public function receipts()
    {
        return $this->hasMany(AppealFamilyJudgmentReceipt::class, 'judgment_id');
    }

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
