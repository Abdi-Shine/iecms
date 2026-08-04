<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The 382 migrated Penal Code article rows had sub_case = null. Each
     * article's description is already the specific offense name (e.g.
     * "Murder", "Theft") — exactly what Sub Case is for — so it's copied
     * across rather than inventing a separate bucketed taxonomy.
     */
    public function up(): void
    {
        DB::table('case_categories')
            ->where('case_name', 'Ciqaabta')
            ->whereNotNull('rule')
            ->whereNull('sub_case')
            ->orderBy('id')
            ->get(['id', 'description'])
            ->each(fn ($row) => DB::table('case_categories')
                ->where('id', $row->id)
                ->update(['sub_case' => $row->description]));
    }

    public function down(): void
    {
        DB::table('case_categories')
            ->where('case_name', 'Ciqaabta')
            ->whereNotNull('rule')
            ->update(['sub_case' => null]);
    }
};
