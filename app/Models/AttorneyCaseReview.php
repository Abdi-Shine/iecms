<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttorneyCaseReview extends Model
{
    protected $table   = 'attorney_case_reviews';
    protected $guarded = [];

    protected $casts = [
        'review_date' => 'date',
    ];

    public function attorneyCase()
    {
        return $this->belongsTo(AttorneyCase::class, 'attorney_case_id', 'ACID');
    }

    public function department()
    {
        return $this->belongsTo(AttorneyDepartment::class, 'attorney_department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
