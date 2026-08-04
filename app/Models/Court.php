<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string  $courtcode
 * @property string  $longName
 * @property string|null $arabic_name
 * @property string|null $logo
 * @property string|null $stamp
 * @property string|null $letterhead
 * @property string|null $address
 * @property string|null $email
 * @property string|null $website
 * @property string|null $telephone
 */
class Court extends Model
{
    protected $table      = 'courts';
    protected $primaryKey = 'CAI';
    public    $timestamps = false;
    protected $guarded    = [];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'courtID', 'courtcode');
    }
}
