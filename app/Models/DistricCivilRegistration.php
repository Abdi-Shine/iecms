<?php

namespace App\Models;

use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class DistricCivilRegistration extends Model
{
    use BelongsToInstitution;

    protected $primaryKey = 'CRID';
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(Court::class, 'GradeCourt', 'courtcode');
    }

    public function parties()
    {
        return $this->hasMany(CivilCaseParty::class, 'civil_case_id', 'CRID');
    }

    public function documents()
    {
        return $this->hasMany(CivilCaseDocument::class, 'civil_case_id', 'CRID');
    }

    public function lawyers()
    {
        return $this->hasMany(CivilCaseLawyer::class, 'civil_case_id', 'CRID');
    }

    public function legalRepresentatives()
    {
        return $this->hasMany(CivilLegalRepresentative::class, 'civil_case_id', 'CRID');
    }

    public function assignments()
    {
        return $this->hasMany(CivilCaseAssignment::class, 'civil_case_id', 'CRID');
    }

    public function handover()
    {
        return $this->hasOne(CivilCaseHandover::class, 'civil_case_id', 'CRID');
    }

    public function returnFile()
    {
        return $this->hasOne(CivilCaseReturnFile::class, 'civil_case_id', 'CRID');
    }

    public function closeCase()
    {
        return $this->hasOne(CivilCaseCloseCase::class, 'civil_case_id', 'CRID');
    }

    public function enforcement()
    {
        return $this->hasOne(CivilCaseEnforcement::class, 'civil_case_id', 'CRID');
    }

    public function appeal()
    {
        return $this->hasOne(CivilCaseAppeal::class, 'civil_case_id', 'CRID');
    }

    public function transfer()
    {
        return $this->hasOne(CivilCaseTransfer::class, 'civil_case_id', 'CRID');
    }

    public function hearings()
    {
        return $this->hasMany(Hearing::class, 'civil_case_id', 'CRID');
    }

    public function judgments()
    {
        return $this->hasMany(Judgment::class, 'civil_case_id', 'CRID');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'civil_case_id', 'CRID');
    }

    public function districtCivilPayments()
    {
        return $this->hasMany(DistrictCivilPayment::class, 'civil_case_id', 'CRID');
    }
}
