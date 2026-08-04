<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourtTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Supreme Court',
            'Appellate Court',
            'High Court',
            'Regional Court',
            'District Court',
        ];

        foreach ($types as $name) {
            DB::table('court_types')->insertOrIgnore([
                'name'       => $name,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
