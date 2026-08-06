<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Model;

class CriminalDetainee extends Model
{
    use BelongsToInstitution, Auditable;

    protected $table   = 'criminal_detainees';
    protected $guarded = [];

    public const STATUSES = ['Newly Admitted', 'Remanded', 'Awaiting Bail Hearing', 'Granted Bail', 'Released', 'Transferred', 'Deceased'];

    protected static function booted(): void
    {
        static::creating(function ($detainee) {
            if (!$detainee->detainee_number) {
                $detainee->detainee_number = static::nextDetaineeNumber($detainee->institution_id);
            }
        });
    }

    public static function nextDetaineeNumber(?int $institutionId = null): string
    {
        $config = CriminalNumberFormat::configFor('detainee_id', $institutionId);
        $searchPrefix = CriminalNumberFormat::searchPrefix($config);

        $last = static::withoutGlobalScopes()
            ->where('detainee_number', 'like', $searchPrefix . '%')
            ->orderByDesc('id')
            ->value('detainee_number');

        $serial = 1;
        if ($last) {
            $serial = intval(substr($last, strlen($searchPrefix))) + 1;
        }

        CriminalNumberFormat::markUsed('detainee_id', $institutionId);

        return CriminalNumberFormat::format($config, $serial);
    }

    protected function casts(): array
    {
        return [
            'admission_datetime'         => 'datetime',
            'legal_deadline'             => 'date',
            'property_receipt_signed'    => 'boolean',
            'medical_screening_referred' => 'boolean',
        ];
    }

    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class);
    }

    public function propertyItems()
    {
        return $this->hasMany(CriminalDetaineePropertyItem::class, 'detainee_id');
    }

    public function transfers()
    {
        return $this->hasMany(CriminalDetaineeTransfer::class, 'detainee_id')->orderByDesc('transfer_datetime');
    }

    public function release()
    {
        return $this->hasOne(CriminalDetaineeRelease::class, 'detainee_id');
    }

    public function remandOrders()
    {
        return $this->hasMany(CriminalDetaineeRemandOrder::class, 'detainee_id')->orderByDesc('created_at');
    }

    public function exhibits()
    {
        return $this->hasMany(CriminalDetaineeExhibit::class, 'detainee_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(CriminalDetaineeMedicalRecord::class, 'detainee_id')->orderByDesc('visit_date');
    }

    /**
     * Drives the sidebar's "pending admissions" red badge count —
     * a newly admitted detainee whose intake isn't fully processed yet
     * (property receipt not signed, or medical screening not referred).
     */
    public function isPending(): bool
    {
        return $this->custody_status === 'Newly Admitted'
            && (!$this->property_receipt_signed || !$this->medical_screening_referred);
    }
}
