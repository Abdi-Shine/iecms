<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    protected $table      = 'lawyers';
    protected $primaryKey = 'LRID';
    public    $timestamps = false;
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(Court::class, 'CourtID', 'courtcode');
    }
}
