<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Dedicated permission for the AGO 'Guriga' dashboard, separate from
     * 'Attorney Case Registration' (which grants full case CRUD). Mirrors the
     * generic 'Dashboard' module: a single view-only action. Roles that
     * already had case-registration access are granted this too, so nobody
     * loses access to the dashboard they could already see.
     */
    public function up(): void
    {
        $sort = DB::table('permissions')->max('sort_order') + 1;
        $now  = now();

        $permissionId = DB::table('permissions')->insertGetId([
            'module'       => 'Attorney Dashboard',
            'action'       => 'view',
            'display_name' => 'View',
            'sort_order'   => $sort,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $roleIds = DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('permissions.module', 'Attorney Case Registration')
            ->where('permissions.action', 'view')
            ->pluck('role_permissions.role_id');

        foreach ($roleIds as $roleId) {
            DB::table('role_permissions')->insert([
                'role_id'       => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    public function down(): void
    {
        $id = DB::table('permissions')->where('module', 'Attorney Dashboard')->value('id');
        DB::table('role_permissions')->where('permission_id', $id)->delete();
        DB::table('permissions')->where('module', 'Attorney Dashboard')->delete();
    }
};
