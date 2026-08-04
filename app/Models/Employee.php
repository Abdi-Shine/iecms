<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'AID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'DOB' => 'date',
        'Dates' => 'date',
    ];

    /**
     * Get the court associated with the employee.
     */
    public function court()
    {
        return $this->belongsTo(Court::class, 'courtID', 'courtcode');
    }

    public function assignments()
    {
        return $this->hasMany(CivilCaseAssignment::class, 'employee_id', 'AID');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'system_username');
    }

    public function attorneyCaseAssignments()
    {
        return $this->hasMany(AttorneyCaseProsecutor::class, 'prosecutor_id', 'AID');
    }
}
