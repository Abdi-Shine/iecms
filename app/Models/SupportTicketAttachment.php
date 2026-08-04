<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketAttachment extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'support_ticket_reply_id',
        'uploaded_by',
        'original_name',
        'file_path',
        'mime_type',
        'size',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function reply()
    {
        return $this->belongsTo(SupportTicketReply::class, 'support_ticket_reply_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
