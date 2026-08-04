<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $guarded = [];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
