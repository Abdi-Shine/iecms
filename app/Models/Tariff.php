<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = ['court_id', 'tariff_code', 'name_so', 'amount', 'type', 'currency', 'status', 'description'];

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id', 'CAI');
    }
}
