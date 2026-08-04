<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Appeal Civil sidebar section previously reused the District Civil
     * modules below, so it had no permissions of its own and never showed
     * up in the Role & Permission Matrix. This adds a dedicated module per
     * Appeal Civil sidebar item, mirroring whichever roles currently hold
     * the shared module so nobody's access changes as a result.
     */
    public function up(): void
    {
        $modules = [
            'Appeal Civil Registration' => ['view', 'create', 'edit', 'delete'],
            'Appeal Case Handover'      => ['view', 'create', 'edit', 'delete'],
            'Appeal Case Assignment'    => ['view', 'create', 'edit', 'delete'],
            'Appeal Hearings'           => ['view', 'create', 'edit', 'delete'],
            'Appeal Judgments'          => ['view', 'create', 'edit', 'delete'],
            'Appeal Return File'        => ['view', 'manage'],
            'Appeal Close Case'         => ['view', 'manage'],
            'Appeal Enforcement'        => ['view', 'create', 'edit', 'delete'],
            'Appeal Cases'              => ['view', 'create', 'edit', 'delete'],
        ];

        $mirrorOf = [
            'Appeal Civil Registration' => 'Civil Case Registration',
            'Appeal Case Handover'      => 'Case Handover',
            'Appeal Case Assignment'    => 'Case Assignment',
            'Appeal Hearings'           => 'Hearings',
            'Appeal Judgments'          => 'Judgments',
            'Appeal Return File'        => 'Return Civil File',
            'Appeal Close Case'         => 'Close Case',
            'Appeal Enforcement'        => 'Enforcement',
            'Appeal Cases'              => 'Enforcement',
        ];

        $sort   = DB::table('permissions')->max('sort_order') + 1;
        $labels = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete', 'manage' => 'Manage'];
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
        $modules = [
            'Appeal Civil Registration', 'Appeal Case Handover', 'Appeal Case Assignment',
            'Appeal Hearings', 'Appeal Judgments', 'Appeal Return File', 'Appeal Close Case',
            'Appeal Enforcement', 'Appeal Cases',
        ];

        $ids = DB::table('permissions')->whereIn('module', $modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
