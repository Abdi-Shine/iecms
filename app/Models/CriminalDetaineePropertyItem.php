<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriminalDetaineePropertyItem extends Model
{
    protected $table   = 'criminal_detainee_property_items';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'returned'    => 'boolean',
            'returned_at' => 'datetime',
        ];
    }

    public function detainee()
    {
        return $this->belongsTo(CriminalDetainee::class, 'detainee_id');
    }
}
