<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalDetaineeRelease extends Model
{
    use Auditable;

    protected $table   = 'criminal_detainee_releases';
    protected $guarded = [];

    public const TYPES = ['Court Order', 'Bail Granted', 'Charges Dropped', 'Expiry of Remand Period', 'Transfer to Correctional Services'];

    protected function casts(): array
    {
        return [
            'released_at'                  => 'datetime',
            'property_returned_confirmed'  => 'boolean',
        ];
    }

    public function detainee()
    {
        return $this->belongsTo(CriminalDetainee::class, 'detainee_id');
    }
}
