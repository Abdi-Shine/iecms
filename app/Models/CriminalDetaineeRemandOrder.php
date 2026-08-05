<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalDetaineeRemandOrder extends Model
{
    use Auditable;

    protected $table   = 'criminal_detainee_remand_orders';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'remand_start_date' => 'date',
            'expiry_date'       => 'date',
        ];
    }

    public function detainee()
    {
        return $this->belongsTo(CriminalDetainee::class, 'detainee_id');
    }

    public function renewedFrom()
    {
        return $this->belongsTo(self::class, 'renewal_of');
    }

    public function alertLevel(): ?string
    {
        if (!$this->expiry_date) return null;

        $hours = now()->diffInHours($this->expiry_date, false);
        if ($hours < 0) return 'expired';
        if ($hours <= 24) return 'critical';
        if ($hours <= 48) return 'warning';
        if ($hours <= 72) return 'notice';
        return null;
    }
}
