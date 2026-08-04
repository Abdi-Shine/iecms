<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAttachment extends Model
{
    protected $table      = 'title_documents_attachments';
    protected $primaryKey = 'AID';
    protected $guarded    = [];

    public function court()
    {
        return $this->belongsTo(\App\Models\Court::class, 'courtID', 'courtcode');
    }
}
