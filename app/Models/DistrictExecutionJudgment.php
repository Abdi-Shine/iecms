<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionJudgment extends Model
{
    protected $table   = 'district_execution_judgments';
    protected $guarded = [];

    public function executionCase()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }

    public function receipts()
    {
        return $this->hasMany(DistrictExecutionJudgmentReceipt::class, 'judgment_id');
    }

    protected $casts = [];

    protected static function booted(): void
    {
        static::creating(function ($judgment) {
            $next = static::max('id') + 1;
            $judgment->form_id = 'JDGE-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$judgment->created_by) {
                $judgment->created_by = auth()->user()->name ?? null;
            }
        });
    }
}
