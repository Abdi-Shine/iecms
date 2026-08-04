<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'group_roles');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'group_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
