<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * A single shared "Institution Admin" role, reused across every
     * institution via institution-scoped Groups (one Group per
     * institution, each with this role attached via group_roles).
     * Baseline covers the provisioning workflow's later steps —
     * managing staff, granting logins, assigning roles — without
     * operational case-management access.
     */
    public function up(): void
    {
        $now = now();

        $roleId = DB::table('roles')->insertGetId([
            'name'         => 'Institution Admin',
            'display_name' => 'Institution Admin',
            'color'        => '#0EA5E9',
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $grants = [
            ['module' => 'Dashboard', 'action' => 'view'],
            ['module' => 'Staff Registry', 'action' => 'view'],
            ['module' => 'Staff Registry', 'action' => 'create'],
            ['module' => 'Staff Registry', 'action' => 'edit'],
            ['module' => 'Access Login', 'action' => 'view'],
            ['module' => 'Access Login', 'action' => 'manage'],
            ['module' => 'Role & Permission', 'action' => 'view'],
            ['module' => 'Role & Permission', 'action' => 'manage'],
        ];

        foreach ($grants as $grant) {
            $permissionId = DB::table('permissions')
                ->where('module', $grant['module'])
                ->where('action', $grant['action'])
                ->value('id');

            if ($permissionId) {
                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $roleId = DB::table('roles')->where('name', 'Institution Admin')->value('id');

        if ($roleId) {
            DB::table('role_permissions')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }
    }
};
