<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilJudgment extends Model
{
    protected $table   = 'appeal_civil_judgments';
    protected $guarded = [];

    protected $casts = [];

    public function civilCase()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID');
    }

    public function receipts()
    {
        return $this->hasMany(AppealCivilJudgmentReceipt::class, 'judgment_id');
    }

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
