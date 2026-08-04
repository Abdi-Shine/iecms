<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Dedicated permission module per District Execution sidebar item, mirroring
     * whichever roles currently hold the equivalent District Civil module so
     * existing role holders get matching access to the new Execution module out
     * of the box (same pattern as add_district_family_permission_modules.php).
     */
    public function up(): void
    {
        $modules = [
            'Execution Case Registration'      => ['view', 'create', 'edit', 'delete'],
            'Execution Case Handover'          => ['view', 'create', 'edit', 'delete'],
            'Execution Case Handover Approval' => ['view', 'create', 'edit', 'delete'],
            'Execution Case Assignment'        => ['view', 'create', 'edit', 'delete'],
            'Execution Hearings'               => ['view', 'create', 'edit', 'delete'],
            'Execution Judgments'              => ['view', 'create', 'edit', 'delete'],
            'Execution Judgment Receipts'      => ['view', 'create', 'edit', 'delete'],
            'Execution Close Case'             => ['view', 'create', 'edit', 'delete'],
            'Execution Return File'            => ['view', 'create', 'edit', 'delete'],
            'Execution Enforcement'            => ['view', 'create', 'edit', 'delete'],
            'Execution Cases'                  => ['view', 'create', 'edit', 'delete'],
        ];

        $mirrorOf = [
            'Execution Case Registration'      => 'Civil Case Registration',
            'Execution Case Handover'          => 'Case Handover',
            'Execution Case Handover Approval' => 'Case Handover Approval',
            'Execution Case Assignment'        => 'Case Assignment',
            'Execution Hearings'               => 'Hearings',
            'Execution Judgments'              => 'Judgments',
            'Execution Judgment Receipts'      => 'Judgment Receipts',
            'Execution Close Case'             => 'Close Case',
            'Execution Return File'            => 'Return Civil File',
            'Execution Enforcement'            => 'Enforcement',
            'Execution Cases'                  => 'Appeal',
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
        $modules = [
            'Execution Case Registration', 'Execution Case Handover', 'Execution Case Handover Approval',
            'Execution Case Assignment', 'Execution Hearings', 'Execution Judgments', 'Execution Judgment Receipts',
            'Execution Close Case', 'Execution Return File', 'Execution Enforcement', 'Execution Cases',
        ];

        $ids = DB::table('permissions')->whereIn('module', $modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
