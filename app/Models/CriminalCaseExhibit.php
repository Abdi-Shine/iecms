<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCaseExhibit extends Model
{
    use Auditable;

    protected $table   = 'criminal_case_exhibits';
    protected $guarded = [];

    public const STATUSES = ['Held', 'Sent to Court', 'Returned', 'Disposed'];

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }
}
