<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JudgmentDocumentSignature extends Model
{
    protected $table = 'district_civil_document_signatures';

    protected $fillable = [
        'document_type',
        'document_id',
        'signer_id',
        'role',
        'signed_at',
        'ip_address',
        'content_hash',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function signer()
    {
        return $this->belongsTo(Employee::class, 'signer_id', 'AID');
    }
}
