<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Articles registry was merged into Case Category (see
     * 2026_08_01_110000_merge_articles_into_case_categories.php) and its
     * controller/routes/view were deleted, so this permission module is
     * now orphaned — nothing checks it anymore.
     */
    public function up(): void
    {
        $ids = DB::table('permissions')->where('module', 'Articles')->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->where('module', 'Articles')->delete();
    }

    public function down(): void
    {
        $actions = ['view', 'create', 'edit', 'delete'];
        $labels  = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];
        $sort    = DB::table('permissions')->max('sort_order') + 1;
        $now     = now();

        foreach ($actions as $action) {
            DB::table('permissions')->insert([
                'module'       => 'Articles',
                'action'       => $action,
                'display_name' => $labels[$action],
                'sort_order'   => $sort++,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
};
