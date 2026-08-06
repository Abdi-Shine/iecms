<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Appeal Case Lawyers" is intentionally not duplicated here — Appeal
     * Family's lawyer-assignment routes reuse that existing module, shared
     * across every Appeal case type.
     *
     * Granted to whichever roles currently hold Appeal Civil Registration,
     * so nobody who administers the Appeal Court loses/gains access by
     * accident when this ships — same approach used for Appeal Criminal.
     */
    public function up(): void
    {
        $actions = ['view', 'create', 'edit', 'delete'];
        $labels  = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];
        $sort    = DB::table('permissions')->max('sort_order') + 1;
        $now     = now();

        foreach ($actions as $action) {
            $newId = DB::table('permissions')->insertGetId([
                'module'       => 'Appeal Family Registration',
                'action'       => $action,
                'display_name' => $labels[$action],
                'sort_order'   => $sort++,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            $mirrorId = DB::table('permissions')
                ->where('module', 'Appeal Civil Registration')
                ->where('action', $action)
                ->value('id');

            if ($mirrorId) {
                $roleIds = DB::table('role_permissions')->where('permission_id', $mirrorId)->pluck('role_id');
                foreach ($roleIds as $roleId) {
                    DB::table('role_permissions')->insert([
                        'role_id'       => $roleId,
                        'permission_id' => $newId,
                    ]);
                }
            }
        }

        // Also grant to the "Appeal Court Admin" role from Phase 2, which
        // was defined with the Appeal Criminal Registration grant list at
        // the time and hasn't been kept in sync since.
        $appealAdminRoleId = DB::table('roles')->where('name', 'appeal_admin_')->value('id');
        if ($appealAdminRoleId) {
            $newIds = DB::table('permissions')->where('module', 'Appeal Family Registration')->pluck('id');
            foreach ($newIds as $permId) {
                $exists = DB::table('role_permissions')->where('role_id', $appealAdminRoleId)->where('permission_id', $permId)->exists();
                if (!$exists) {
                    DB::table('role_permissions')->insert(['role_id' => $appealAdminRoleId, 'permission_id' => $permId]);
                }
            }
        }
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->where('module', 'Appeal Family Registration')->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->where('module', 'Appeal Family Registration')->delete();
    }
};
