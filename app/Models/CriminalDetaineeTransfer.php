<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalDetaineeTransfer extends Model
{
    use Auditable;

    protected $table   = 'criminal_detainee_transfers';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'transfer_datetime'           => 'datetime',
            'receiving_officer_confirmed' => 'boolean',
        ];
    }

    public function detainee()
    {
        return $this->belongsTo(CriminalDetainee::class, 'detainee_id');
    }
}
