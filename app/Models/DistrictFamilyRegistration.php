<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyRegistration extends Model
{
    protected $table      = 'district_family_registrations';
    protected $primaryKey = 'FCID';
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(Court::class, 'GradeCourt', 'courtcode');
    }

    public function parties()
    {
        return $this->hasMany(DistrictFamilyParty::class, 'family_case_id', 'FCID');
    }

    public function documents()
    {
        return $this->hasMany(DistrictFamilyDocument::class, 'family_case_id', 'FCID');
    }

    public function lawyers()
    {
        return $this->hasMany(DistrictFamilyLawyer::class, 'family_case_id', 'FCID');
    }

    public function legalRepresentatives()
    {
        return $this->hasMany(DistrictFamilyLegalRepresentative::class, 'family_case_id', 'FCID');
    }

    public function assignments()
    {
        return $this->hasMany(DistrictFamilyAssignment::class, 'family_case_id', 'FCID');
    }

    public function handover()
    {
        return $this->hasOne(DistrictFamilyHandover::class, 'family_case_id', 'FCID');
    }

    public function returnFile()
    {
        return $this->hasOne(DistrictFamilyReturnFile::class, 'family_case_id', 'FCID');
    }

    public function closeCase()
    {
        return $this->hasOne(DistrictFamilyCloseCase::class, 'family_case_id', 'FCID');
    }

    public function enforcement()
    {
        return $this->hasOne(DistrictFamilyEnforcement::class, 'family_case_id', 'FCID');
    }

    public function appeal()
    {
        return $this->hasOne(DistrictFamilyAppeal::class, 'family_case_id', 'FCID');
    }

    public function transfer()
    {
        return $this->hasOne(DistrictFamilyTransfer::class, 'family_case_id', 'FCID');
    }

    public function hearings()
    {
        return $this->hasMany(DistrictFamilyHearing::class, 'family_case_id', 'FCID');
    }

    public function judgments()
    {
        return $this->hasMany(DistrictFamilyJudgment::class, 'family_case_id', 'FCID');
    }

    public function payments()
    {
        return $this->hasMany(DistrictFamilyPayment::class, 'family_case_id', 'FCID');
    }
}
