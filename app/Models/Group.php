<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use BelongsToInstitution, Auditable;

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
