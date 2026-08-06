<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class AppealCriminalRegistration extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table      = 'appeal_criminal_registrations';
    protected $primaryKey = 'ACMID';
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(Court::class, 'GradeCourt', 'courtcode');
    }

    public function parties()
    {
        return $this->hasMany(AppealCriminalParty::class, 'criminal_case_id', 'ACMID');
    }

    public function documents()
    {
        return $this->hasMany(AppealCriminalDocument::class, 'criminal_case_id', 'ACMID');
    }

    public function lawyers()
    {
        return $this->hasMany(AppealCriminalLawyer::class, 'criminal_case_id', 'ACMID');
    }

    public function legalRepresentatives()
    {
        return $this->hasMany(AppealCriminalLegalRepresentative::class, 'criminal_case_id', 'ACMID');
    }

    // The relations below belong to stages not built yet (Assignment,
    // Handover, Hearing, Judgment, Return File, Close Case, Enforcement,
    // further-Appeal, Transfer) — defined now, matching AppealCivilRegistration's
    // full shape, so later phases only need to add the target model, not
    // touch this one. Harmless while unused: Eloquent only resolves a
    // relation's target class when the relation is actually called.
    public function assignments()
    {
        return $this->hasMany(AppealCriminalAssignment::class, 'criminal_case_id', 'ACMID');
    }

    public function handover()
    {
        return $this->hasOne(AppealCriminalHandover::class, 'criminal_case_id', 'ACMID');
    }

    public function hearings()
    {
        return $this->hasMany(AppealCriminalHearing::class, 'criminal_case_id', 'ACMID');
    }

    public function judgments()
    {
        return $this->hasMany(AppealCriminalJudgment::class, 'criminal_case_id', 'ACMID');
    }

    public function returnFile()
    {
        return $this->hasOne(AppealCriminalReturnFile::class, 'criminal_case_id', 'ACMID');
    }

    public function closeCase()
    {
        return $this->hasOne(AppealCriminalCloseCase::class, 'criminal_case_id', 'ACMID');
    }

    public function enforcement()
    {
        return $this->hasOne(AppealCriminalEnforcement::class, 'criminal_case_id', 'ACMID');
    }

    public function appeal()
    {
        return $this->hasOne(AppealCriminalAppeal::class, 'criminal_case_id', 'ACMID');
    }

    public function transfer()
    {
        return $this->hasOne(AppealCriminalTransfer::class, 'criminal_case_id', 'ACMID');
    }
}
