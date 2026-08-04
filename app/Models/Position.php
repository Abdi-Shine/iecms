<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table      = 'typeofposition';
    protected $primaryKey = 'PID';
    public $timestamps    = false;

    protected $fillable = [
        'Pcode', 'Pname', 'addedBy', 'addedDate', 'updatedBy', 'updatedDate',
    ];
}
