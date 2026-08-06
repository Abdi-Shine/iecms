<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalHandover extends Model
{
    protected $table    = 'appeal_criminal_handovers';
    protected $fillable = [
        'criminal_case_id', 'documents', 'special_instructions', 'additional_notes', 'status', 'created_by',
    ];

    protected $casts = ['documents' => 'array'];

    public function case()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID')->with('court');
    }
}
