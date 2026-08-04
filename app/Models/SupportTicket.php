<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($ticket) {
            $next = static::max('id') + 1;
            $ticket->ticket_number = 'HD-' . date('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            if (!$ticket->status) {
                $ticket->status = 'Open';
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class)->orderBy('created_at');
    }

    public function attachments()
    {
        return $this->hasMany(SupportTicketAttachment::class)->whereNull('support_ticket_reply_id');
    }

    public function activities()
    {
        return $this->hasMany(SupportTicketActivity::class)->orderBy('created_at');
    }
}
