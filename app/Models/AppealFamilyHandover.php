<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealFamilyHandover extends Model
{
    protected $table    = 'appeal_family_handovers';
    protected $fillable = [
        'family_case_id', 'documents', 'special_instructions', 'additional_notes', 'status', 'created_by',
    ];

    protected $casts = ['documents' => 'array'];

    public function case()
    {
        return $this->belongsTo(AppealFamilyRegistration::class, 'family_case_id', 'AFCID')->with('court');
    }
}
