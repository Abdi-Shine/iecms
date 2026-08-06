<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppealCriminalReturnFile extends Model
{
    protected $table    = 'appeal_criminal_return_files';
    protected $fillable = [
        'criminal_case_id', 'documents', 'special_instructions', 'additional_notes', 'status', 'created_by',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function case()
    {
        return $this->belongsTo(AppealCriminalRegistration::class, 'criminal_case_id', 'ACMID')->with('court');
    }
}
