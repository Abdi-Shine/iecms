<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class DistrictExecutionRegistration extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table      = 'district_execution_registrations';
    protected $primaryKey = 'ECID';
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(Court::class, 'GradeCourt', 'courtcode');
    }

    public function parties()
    {
        return $this->hasMany(DistrictExecutionParty::class, 'execution_case_id', 'ECID');
    }

    public function documents()
    {
        return $this->hasMany(DistrictExecutionDocument::class, 'execution_case_id', 'ECID');
    }

    public function lawyers()
    {
        return $this->hasMany(DistrictExecutionLawyer::class, 'execution_case_id', 'ECID');
    }

    public function legalRepresentatives()
    {
        return $this->hasMany(DistrictExecutionLegalRepresentative::class, 'execution_case_id', 'ECID');
    }

    public function assignments()
    {
        return $this->hasMany(DistrictExecutionAssignment::class, 'execution_case_id', 'ECID');
    }

    public function handover()
    {
        return $this->hasOne(DistrictExecutionHandover::class, 'execution_case_id', 'ECID');
    }

    public function returnFile()
    {
        return $this->hasOne(DistrictExecutionReturnFile::class, 'execution_case_id', 'ECID');
    }

    public function closeCase()
    {
        return $this->hasOne(DistrictExecutionCloseCase::class, 'execution_case_id', 'ECID');
    }

    public function enforcement()
    {
        return $this->hasOne(DistrictExecutionEnforcement::class, 'execution_case_id', 'ECID');
    }

    public function appeal()
    {
        return $this->hasOne(DistrictExecutionAppeal::class, 'execution_case_id', 'ECID');
    }

    public function transfer()
    {
        return $this->hasOne(DistrictExecutionTransfer::class, 'execution_case_id', 'ECID');
    }

    public function hearings()
    {
        return $this->hasMany(DistrictExecutionHearing::class, 'execution_case_id', 'ECID');
    }

    public function judgments()
    {
        return $this->hasMany(DistrictExecutionJudgment::class, 'execution_case_id', 'ECID');
    }

    public function payments()
    {
        return $this->hasMany(DistrictExecutionPayment::class, 'execution_case_id', 'ECID');
    }
}
