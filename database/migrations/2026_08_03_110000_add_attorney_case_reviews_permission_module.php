<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Dedicated permission module for Hubinta Cabashada (case reviews),
     * mirroring whichever roles currently hold 'Attorney Case Registration'
     * so existing Attorney office role holders get matching access out of the box.
     */
    public function up(): void
    {
        $actions = ['view', 'create', 'edit', 'delete'];
        $labels  = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];
        $sort    = DB::table('permissions')->max('sort_order') + 1;
        $now     = now();

        foreach ($actions as $action) {
            $newId = DB::table('permissions')->insertGetId([
                'module'       => 'Attorney Case Reviews',
                'action'       => $action,
                'display_name' => $labels[$action],
                'sort_order'   => $sort++,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            $oldPermId = DB::table('permissions')
                ->where('module', 'Attorney Case Registration')
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

    public function down(): void
    {
        $ids = DB::table('permissions')->where('module', 'Attorney Case Reviews')->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->where('module', 'Attorney Case Reviews')->delete();
    }
};
