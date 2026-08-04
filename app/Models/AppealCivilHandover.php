<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCivilHandover extends Model
{
    protected $table    = 'appeal_civil_handovers';
    protected $fillable = [
        'civil_case_id', 'documents', 'special_instructions', 'additional_notes', 'status', 'created_by',
    ];

    protected $casts = ['documents' => 'array'];

    public function case()
    {
        return $this->belongsTo(AppealCivilRegistration::class, 'civil_case_id', 'ACID')->with('court');
    }
}
