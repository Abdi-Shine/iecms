<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalJudgment extends Model
{
    protected $table   = 'appeal_criminal_judgments';
    protected $guarded = [];

    public function criminalCase()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID');
    }

    public function receipts()
    {
        return $this->hasMany(AppealCriminalJudgmentReceipt::class, 'judgment_id');
    }

    protected static function booted(): void
    {
        static::creating(function ($judgment) {
            $next = static::max('id') + 1;
            $judgment->form_id = 'JDGC-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$judgment->created_by) {
                $judgment->created_by = auth()->user()->name ?? null;
            }
        });
    }
}
