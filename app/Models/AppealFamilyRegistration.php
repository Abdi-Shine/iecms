<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class AppealFamilyRegistration extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table      = 'appeal_family_registrations';
    protected $primaryKey = 'AFCID';
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(Court::class, 'GradeCourt', 'courtcode');
    }

    public function parties()
    {
        return $this->hasMany(AppealFamilyParty::class, 'family_case_id', 'AFCID');
    }

    public function documents()
    {
        return $this->hasMany(AppealFamilyDocument::class, 'family_case_id', 'AFCID');
    }

    public function lawyers()
    {
        return $this->hasMany(AppealFamilyLawyer::class, 'family_case_id', 'AFCID');
    }

    public function legalRepresentatives()
    {
        return $this->hasMany(AppealFamilyLegalRepresentative::class, 'family_case_id', 'AFCID');
    }

    // The relations below belong to stages not built yet (Assignment,
    // Handover, Hearing, Judgment, Return File, Close Case, Enforcement,
    // further-Appeal, Transfer) — defined now, matching AppealCriminalRegistration's
    // full shape, so later phases only need to add the target model, not
    // touch this one. Harmless while unused: Eloquent only resolves a
    // relation's target class when the relation is actually called.
    public function assignments()
    {
        return $this->hasMany(AppealFamilyAssignment::class, 'family_case_id', 'AFCID');
    }

    public function handover()
    {
        return $this->hasOne(AppealFamilyHandover::class, 'family_case_id', 'AFCID');
    }

    public function hearings()
    {
        return $this->hasMany(AppealFamilyHearing::class, 'family_case_id', 'AFCID');
    }

    public function judgments()
    {
        return $this->hasMany(AppealFamilyJudgment::class, 'family_case_id', 'AFCID');
    }

    public function returnFile()
    {
        return $this->hasOne(AppealFamilyReturnFile::class, 'family_case_id', 'AFCID');
    }

    public function closeCase()
    {
        return $this->hasOne(AppealFamilyCloseCase::class, 'family_case_id', 'AFCID');
    }

    public function enforcement()
    {
        return $this->hasOne(AppealFamilyEnforcement::class, 'family_case_id', 'AFCID');
    }

    public function appeal()
    {
        return $this->hasOne(AppealFamilyAppeal::class, 'family_case_id', 'AFCID');
    }

    public function transfer()
    {
        return $this->hasOne(AppealFamilyTransfer::class, 'family_case_id', 'AFCID');
    }
}
