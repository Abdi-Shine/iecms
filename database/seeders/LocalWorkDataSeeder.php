<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalWorkDataSeeder extends Seeder
{
    /**
     * Data-only fixtures exported from local development, for tables that
     * don't hold migration-seeded reference data. insertOrIgnore keeps this
     * safe to re-run without duplicating rows already present.
     */
    public function run(): void
    {
        $dir = __DIR__ . '/data';
        $manifest = json_decode(file_get_contents("$dir/_manifest.json"), true);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($manifest as $table) {
                $rows = json_decode(file_get_contents("$dir/$table.json"), true);

                if (empty($rows)) {
                    continue;
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table($table)->insertOrIgnore($chunk);
                }

                $this->command?->info("Seeded $table: " . count($rows) . ' rows');
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
