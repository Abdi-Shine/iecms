<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalRegistration extends Model
{
    protected $table      = 'district_criminal_registrations';
    protected $primaryKey = 'CMID';
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(Court::class, 'GradeCourt', 'courtcode');
    }

    public function parties()
    {
        return $this->hasMany(DistrictCriminalParty::class, 'criminal_case_id', 'CMID');
    }

    public function documents()
    {
        return $this->hasMany(DistrictCriminalDocument::class, 'criminal_case_id', 'CMID');
    }

    public function lawyers()
    {
        return $this->hasMany(DistrictCriminalLawyer::class, 'criminal_case_id', 'CMID');
    }

    public function legalRepresentatives()
    {
        return $this->hasMany(DistrictCriminalLegalRepresentative::class, 'criminal_case_id', 'CMID');
    }

    public function assignments()
    {
        return $this->hasMany(DistrictCriminalAssignment::class, 'criminal_case_id', 'CMID');
    }

    public function handover()
    {
        return $this->hasOne(DistrictCriminalHandover::class, 'criminal_case_id', 'CMID');
    }

    public function returnFile()
    {
        return $this->hasOne(DistrictCriminalReturnFile::class, 'criminal_case_id', 'CMID');
    }

    public function closeCase()
    {
        return $this->hasOne(DistrictCriminalCloseCase::class, 'criminal_case_id', 'CMID');
    }

    public function enforcement()
    {
        return $this->hasOne(DistrictCriminalEnforcement::class, 'criminal_case_id', 'CMID');
    }

    public function appeal()
    {
        return $this->hasOne(DistrictCriminalAppeal::class, 'criminal_case_id', 'CMID');
    }

    public function transfer()
    {
        return $this->hasOne(DistrictCriminalTransfer::class, 'criminal_case_id', 'CMID');
    }

    public function hearings()
    {
        return $this->hasMany(DistrictCriminalHearing::class, 'criminal_case_id', 'CMID');
    }

    public function judgments()
    {
        return $this->hasMany(DistrictCriminalJudgment::class, 'criminal_case_id', 'CMID');
    }

    public function payments()
    {
        return $this->hasMany(DistrictCriminalPayment::class, 'criminal_case_id', 'CMID');
    }
}
