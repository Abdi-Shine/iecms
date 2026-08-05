<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalCaseInterview extends Model
{
    use Auditable;

    protected $table   = 'criminal_case_interviews';
    protected $guarded = [];

    public const ROLES   = ['suspect', 'witness', 'complainant', 'informant'];
    public const FORMATS = ['written', 'audio', 'video', 'transcribed'];

    protected function casts(): array
    {
        return [
            'interviewee_id' => 'encrypted',
            'interview_date' => 'date',
            'signed_off_at'  => 'datetime',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function signedOffBy()
    {
        return $this->belongsTo(User::class, 'signed_off_by');
    }
}
