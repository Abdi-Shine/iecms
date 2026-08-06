<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class CriminalNumberFormat extends Model
{
    use Auditable;

    protected $table   = 'criminal_number_formats';
    protected $guarded = [];

    public const KEYS = [
        'case_number'     => ['label' => 'Case ID',       'default_prefix' => 'CASE-CID'],
        'ob_number'       => ['label' => 'OB Number',      'default_prefix' => 'OB-CID'],
        'report_number'   => ['label' => 'Report Number',  'default_prefix' => 'RPT-CID'],
        'evidence_id'     => ['label' => 'Evidence ID',    'default_prefix' => 'EV-CID'],
        'detainee_id'     => ['label' => 'Detainee ID',    'default_prefix' => 'DET-CID'],
    ];

    protected function casts(): array
    {
        return [
            'include_year' => 'boolean',
            'locked'       => 'boolean',
        ];
    }

    /**
     * Merges a stored config with sane defaults so callers always get a
     * usable object, whether or not an admin has configured this key yet.
     */
    public static function configFor(string $formatKey, ?int $institutionId): object
    {
        $stored = $institutionId
            ? static::where('institution_id', $institutionId)->where('format_key', $formatKey)->first()
            : null;

        $defaults = self::KEYS[$formatKey] ?? ['label' => $formatKey, 'default_prefix' => strtoupper($formatKey)];

        return (object) [
            'prefix'          => $stored->prefix ?? $defaults['default_prefix'],
            'include_year'    => $stored->include_year ?? true,
            'year_digits'     => $stored->year_digits ?? 4,
            'sequence_length' => $stored->sequence_length ?? 5,
            'reset_period'    => $stored->reset_period ?? 'yearly',
            'locked'          => $stored->locked ?? false,
        ];
    }

    /**
     * Builds the search prefix used to find the last-issued number
     * (respects reset_period) and formats a preview/next number given
     * a serial. Kept separate from the actual sequence lookup, which
     * stays in each model (it queries that model's own table).
     */
    public static function searchPrefix(object $config): string
    {
        $year = $config->include_year ? now()->format($config->year_digits == 2 ? 'y' : 'Y') . '-' : '';
        return $config->reset_period === 'yearly'
            ? $config->prefix . '-' . $year
            : $config->prefix . '-';
    }

    public static function format(object $config, int $serial): string
    {
        return self::searchPrefix($config) . str_pad($serial, $config->sequence_length, '0', STR_PAD_LEFT);
    }

    /**
     * Locks the format the first time it's actually used to generate a
     * number — changing prefix/sequence-length after real numbers exist
     * would break uniqueness/ordering on what's already issued. A row
     * is created (with defaults) if the admin never configured this key,
     * so "locked" always reflects real usage, not just explicit config.
     */
    public static function markUsed(string $formatKey, ?int $institutionId): void
    {
        if (!$institutionId) return;

        $row = static::firstOrCreate(
            ['institution_id' => $institutionId, 'format_key' => $formatKey],
            array_merge(
                ['prefix' => self::KEYS[$formatKey]['default_prefix'] ?? strtoupper($formatKey)],
                ['added_by' => 'System']
            )
        );

        if (!$row->locked) {
            $row->update(['locked' => true]);
        }
    }
}
