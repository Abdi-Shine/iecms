<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The 18 administrative regions of Somalia. Banaadir is already
     * registered, so it's included here only so down() can identify the
     * full set if ever needed — insertOrIgnore skips it on up().
     */
    private array $regions = [
        'Awdal', 'Bakool', 'Banaadir', 'Bari', 'Bay', 'Galguduud', 'Gedo', 'Hiiraan',
        'Lower Juba (Jubbada Hoose)', 'Middle Juba (Jubbada Dhexe)',
        'Lower Shabelle (Shabeellaha Hoose)', 'Middle Shabelle (Shabeellaha Dhexe)',
        'Mudug', 'Nugaal', 'Sanaag', 'Sool', 'Togdheer', 'Woqooyi Galbeed',
    ];

    public function up(): void
    {
        $now = now();

        DB::table('state_regions')->insertOrIgnore(
            array_map(fn ($name) => [
                'state_name' => $name,
                'capital'    => null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $this->regions)
        );
    }

    public function down(): void
    {
        DB::table('state_regions')->whereIn('state_name', $this->regions)->delete();
    }
};
