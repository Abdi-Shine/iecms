<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalDetaineeMedicalRecord extends Model
{
    use Auditable;

    protected $table   = 'criminal_detainee_medical_records';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'visit_date'    => 'date',
            'is_emergency'  => 'boolean',
        ];
    }

    public function detainee()
    {
        return $this->belongsTo(CriminalDetainee::class, 'detainee_id');
    }
}
