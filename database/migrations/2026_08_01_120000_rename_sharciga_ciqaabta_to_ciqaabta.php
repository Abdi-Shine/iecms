<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Requested rename: the 382 migrated Penal Code article rows move from
     * their own "Sharciga Ciqaabta" group into "Ciqaabta" directly. Note:
     * "Ciqaabta" is also the case_name every criminal-subcase dropdown
     * elsewhere in the app plucks from — since these rows have sub_case =
     * null, that dropdown will now include blank entries. Done anyway per
     * explicit instruction.
     */
    public function up(): void
    {
        DB::table('case_categories')
            ->where('case_name', 'Sharciga Ciqaabta')
            ->update(['case_name' => 'Ciqaabta']);
    }

    public function down(): void
    {
        DB::table('case_categories')
            ->where('case_name', 'Ciqaabta')
            ->whereNull('sub_case')
            ->where('rule', 'like', 'Qodobka %')
            ->update(['case_name' => 'Sharciga Ciqaabta']);
    }
};
