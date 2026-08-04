<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictExecutionDocument extends Model
{
    protected $table = 'district_execution_documents';
    protected $primaryKey = 'DID';
    protected $fillable = [
        'execution_case_id',
        'document_name',
        'document_date',
        'description',
        'file_path',
        'addedBy',
        'addedDate',
        'updatedBy',
        'updatedDate'
    ];

    public function case()
    {
        return $this->belongsTo(DistrictExecutionRegistration::class, 'execution_case_id', 'ECID');
    }
}
