<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictCriminalDocument extends Model
{
    protected $table = 'district_criminal_documents';
    protected $primaryKey = 'DID';
    protected $fillable = [
        'criminal_case_id',
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
        return $this->belongsTo(DistrictCriminalRegistration::class, 'criminal_case_id', 'CMID');
    }
}
