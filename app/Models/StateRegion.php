<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateRegion extends Model
{
    protected $guarded = [];

    public function cities()
    {
        return $this->hasMany(City::class, 'state_region_id');
    }
}
