<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class AppealCivilRegistration extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table      = 'appeal_civil_registrations';
    protected $primaryKey = 'ACID';
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(Court::class, 'GradeCourt', 'courtcode');
    }

    public function parties()
    {
        return $this->hasMany(AppealCivilParty::class, 'civil_case_id', 'ACID');
    }

    public function documents()
    {
        return $this->hasMany(AppealCivilDocument::class, 'civil_case_id', 'ACID');
    }

    public function assignments()
    {
        return $this->hasMany(AppealCivilAssignment::class, 'civil_case_id', 'ACID');
    }

    public function handover()
    {
        return $this->hasOne(AppealCivilHandover::class, 'civil_case_id', 'ACID');
    }

    public function hearings()
    {
        return $this->hasMany(AppealCivilHearing::class, 'civil_case_id', 'ACID');
    }

    public function judgments()
    {
        return $this->hasMany(AppealCivilJudgment::class, 'civil_case_id', 'ACID');
    }

    public function returnFile()
    {
        return $this->hasOne(AppealCivilReturnFile::class, 'civil_case_id', 'ACID');
    }

    public function closeCase()
    {
        return $this->hasOne(AppealCivilCloseCase::class, 'civil_case_id', 'ACID');
    }

    public function enforcement()
    {
        return $this->hasOne(AppealCivilEnforcement::class, 'civil_case_id', 'ACID');
    }

    public function appeal()
    {
        return $this->hasOne(AppealCivilAppeal::class, 'civil_case_id', 'ACID');
    }

    public function transfer()
    {
        return $this->hasOne(AppealCivilTransfer::class, 'civil_case_id', 'ACID');
    }

    public function legalRepresentatives()
    {
        return $this->hasMany(AppealCivilLegalRepresentative::class, 'civil_case_id', 'ACID');
    }

    public function lawyers()
    {
        return $this->hasMany(AppealCivilLawyer::class, 'civil_case_id', 'ACID');
    }
}
