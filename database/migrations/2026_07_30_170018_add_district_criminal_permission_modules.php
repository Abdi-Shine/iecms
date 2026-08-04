<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Dedicated permission module per District Criminal sidebar item, mirroring
     * whichever roles currently hold the equivalent District Civil module so
     * existing role holders get matching access to the new Criminal module out
     * of the box (same pattern as add_district_execution_permission_modules.php).
     */
    public function up(): void
    {
        $modules = [
            'Criminal Case Registration'      => ['view', 'create', 'edit', 'delete'],
            'Criminal Case Handover'          => ['view', 'create', 'edit', 'delete'],
            'Criminal Case Handover Approval' => ['view', 'create', 'edit', 'delete'],
            'Criminal Case Assignment'        => ['view', 'create', 'edit', 'delete'],
            'Criminal Hearings'               => ['view', 'create', 'edit', 'delete'],
            'Criminal Judgments'              => ['view', 'create', 'edit', 'delete'],
            'Criminal Judgment Receipts'      => ['view', 'create', 'edit', 'delete'],
            'Criminal Close Case'             => ['view', 'create', 'edit', 'delete'],
            'Criminal Return File'            => ['view', 'create', 'edit', 'delete'],
            'Criminal Enforcement'            => ['view', 'create', 'edit', 'delete'],
            'Criminal Cases'                  => ['view', 'create', 'edit', 'delete'],
        ];

        $mirrorOf = [
            'Criminal Case Registration'      => 'Civil Case Registration',
            'Criminal Case Handover'          => 'Case Handover',
            'Criminal Case Handover Approval' => 'Case Handover Approval',
            'Criminal Case Assignment'        => 'Case Assignment',
            'Criminal Hearings'               => 'Hearings',
            'Criminal Judgments'              => 'Judgments',
            'Criminal Judgment Receipts'      => 'Judgment Receipts',
            'Criminal Close Case'             => 'Close Case',
            'Criminal Return File'            => 'Return Civil File',
            'Criminal Enforcement'            => 'Enforcement',
            'Criminal Cases'                  => 'Appeal',
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
            'Criminal Case Registration', 'Criminal Case Handover', 'Criminal Case Handover Approval',
            'Criminal Case Assignment', 'Criminal Hearings', 'Criminal Judgments', 'Criminal Judgment Receipts',
            'Criminal Close Case', 'Criminal Return File', 'Criminal Enforcement', 'Criminal Cases',
        ];

        $ids = DB::table('permissions')->whereIn('module', $modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
