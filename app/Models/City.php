<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $guarded = [];

    public function region()
    {
        return $this->belongsTo(StateRegion::class, 'state_region_id');
    }
}
