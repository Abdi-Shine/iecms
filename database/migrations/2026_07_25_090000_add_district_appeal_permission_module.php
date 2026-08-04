<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Rafcaanka Dacwada" (District Civil sidebar) reused the "Enforcement"
     * module, so it had no permission of its own and never showed up in the
     * Role & Permission Matrix — the same bug already fixed for the Banadir
     * Appeal Court's equivalent item in
     * 2026_07_08_085950_add_appeal_civil_permission_modules. This gives it
     * (and the /transfer routes grouped with it) a dedicated "Appeal"
     * module, mirroring whichever roles currently hold Enforcement so
     * nobody's access changes as a result.
     */
    public function up(): void
    {
        $module = 'Appeal';
        $mirrorOf = 'Enforcement';
        $labels = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];

        $sort = DB::table('permissions')->max('sort_order') + 1;
        $now = now();

        foreach ($labels as $action => $label) {
            $newId = DB::table('permissions')->insertGetId([
                'module' => $module,
                'action' => $action,
                'display_name' => $label,
                'sort_order' => $sort++,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $oldPermId = DB::table('permissions')
                ->where('module', $mirrorOf)
                ->where('action', $action)
                ->value('id');

            if ($oldPermId) {
                $roleIds = DB::table('role_permissions')->where('permission_id', $oldPermId)->pluck('role_id');
                foreach ($roleIds as $roleId) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $newId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->where('module', 'Appeal')->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->where('module', 'Appeal')->delete();
    }
};
