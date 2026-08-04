<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CivilCaseDocument extends Model
{
    protected $table = 'district_civil_documents';
    protected $primaryKey = 'DID';
    protected $fillable = [
        'civil_case_id',
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
        return $this->belongsTo(DistricCivilRegistration::class, 'civil_case_id', 'CRID');
    }
}
