<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Visibility-only Matrix entries for District Execution's "Case Parties",
     * "Case Documents", and "Case Lawyers" (routes are gated by "Execution
     * Case Registration" / "Execution Case Lawyers" respectively — these exist
     * so the modules are individually listed and grantable in the Role &
     * Permission Matrix), mirroring District Civil's equivalent modules.
     */
    public function up(): void
    {
        $modules = [
            'Execution Case Parties'   => ['view', 'create', 'edit', 'delete'],
            'Execution Case Documents' => ['view', 'create', 'edit', 'delete'],
            'Execution Case Lawyers'   => ['view', 'create', 'edit', 'delete'],
        ];

        $mirrorOf = [
            'Execution Case Parties'   => 'Case Parties',
            'Execution Case Documents' => 'Case Documents',
            'Execution Case Lawyers'   => 'Case Lawyers',
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
        $modules = ['Execution Case Parties', 'Execution Case Documents', 'Execution Case Lawyers'];
        $ids = DB::table('permissions')->whereIn('module', $modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
