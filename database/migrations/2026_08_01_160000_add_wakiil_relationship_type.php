<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * "Wakiil" (representative) covers the case of a child under 14 (no
     * criminal responsibility) where a representative/guardian must be
     * recorded in place of pursuing charges against the child directly.
     */
    public function up(): void
    {
        DB::table('attorney_victim_relationship_types')->insertOrIgnore([
            'name'        => 'Wakiil',
            'value'       => Str::slug('Wakiil'),
            'description' => 'Wakiil/Masuul kii xilku saarnaa halkii dambi loo qaadi lahaa (ilmaha 1-13 sano).',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('attorney_victim_relationship_types')->where('name', 'Wakiil')->delete();
    }
};
