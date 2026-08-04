<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseDocument extends Model
{
    protected $table   = 'attorney_case_documents';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
        ];
    }

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }
}
