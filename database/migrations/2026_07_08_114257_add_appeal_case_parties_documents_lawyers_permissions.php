<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * District Civil has its own Matrix entries for "Case Parties", "Case
     * Documents", and "Case Lawyers" (used mainly for Matrix visibility;
     * the actual routes are gated by "Civil Case Registration"). Appeal
     * Civil never got the equivalents, so they were invisible in the Role
     * & Permission Matrix. "Appeal Case Lawyers" additionally now has a
     * real dedicated route gate (see routes/web.php).
     */
    public function up(): void
    {
        $modules = [
            'Appeal Case Parties'   => ['view', 'create', 'edit', 'delete'],
            'Appeal Case Documents' => ['view', 'create', 'edit', 'delete'],
            'Appeal Case Lawyers'   => ['view', 'create', 'edit', 'delete'],
        ];

        $mirrorOf = [
            'Appeal Case Parties'   => 'Case Parties',
            'Appeal Case Documents' => 'Case Documents',
            'Appeal Case Lawyers'   => 'Case Lawyers',
        ];

        $sort   = DB::table('permissions')->max('sort_order') + 1;
        $labels = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];
        $now    = now();

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $newId = DB::table('permissions')->insertGetId([
                    'module'       => $module,
                    'action'       => $action,
                    'display_name' => $labels[$action],
                    'sort_order'   => $sort++,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                $oldPermId = DB::table('permissions')
                    ->where('module', $mirrorOf[$module])
                    ->where('action', $action)
                    ->value('id');

                if ($oldPermId) {
                    $roleIds = DB::table('role_permissions')->where('permission_id', $oldPermId)->pluck('role_id');
                    foreach ($roleIds as $roleId) {
                        DB::table('role_permissions')->insert([
                            'role_id'       => $roleId,
                            'permission_id' => $newId,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        $modules = ['Appeal Case Parties', 'Appeal Case Documents', 'Appeal Case Lawyers'];
        $ids = DB::table('permissions')->whereIn('module', $modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
