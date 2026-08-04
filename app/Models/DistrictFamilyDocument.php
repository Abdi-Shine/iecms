<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictFamilyDocument extends Model
{
    protected $table = 'district_family_documents';
    protected $primaryKey = 'DID';
    protected $fillable = [
        'family_case_id',
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
        return $this->belongsTo(DistrictFamilyRegistration::class, 'family_case_id', 'FCID');
    }
}
