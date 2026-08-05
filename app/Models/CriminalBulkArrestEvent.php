<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class CriminalBulkArrestEvent extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table   = 'criminal_bulk_arrest_events';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function members()
    {
        return $this->hasMany(CriminalBulkArrestMember::class, 'bulk_arrest_event_id');
    }
}
