<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Dedicated permission module per District Family sidebar item, mirroring
     * whichever roles currently hold the equivalent District Civil module so
     * existing role holders get matching access to the new Family module out
     * of the box (same pattern as add_appeal_civil_permission_modules.php).
     */
    public function up(): void
    {
        $modules = [
            'Family Case Registration'      => ['view', 'create', 'edit', 'delete'],
            'Family Case Handover'          => ['view', 'create', 'edit', 'delete'],
            'Family Case Handover Approval' => ['view', 'create', 'edit', 'delete'],
            'Family Case Assignment'        => ['view', 'create', 'edit', 'delete'],
            'Family Hearings'               => ['view', 'create', 'edit', 'delete'],
            'Family Judgments'              => ['view', 'create', 'edit', 'delete'],
            'Family Judgment Receipts'      => ['view', 'create', 'edit', 'delete'],
            'Family Close Case'             => ['view', 'create', 'edit', 'delete'],
            'Family Return File'            => ['view', 'create', 'edit', 'delete'],
            'Family Enforcement'            => ['view', 'create', 'edit', 'delete'],
            'Family Cases'                  => ['view', 'create', 'edit', 'delete'],
        ];

        $mirrorOf = [
            'Family Case Registration'      => 'Civil Case Registration',
            'Family Case Handover'          => 'Case Handover',
            'Family Case Handover Approval' => 'Case Handover Approval',
            'Family Case Assignment'        => 'Case Assignment',
            'Family Hearings'               => 'Hearings',
            'Family Judgments'              => 'Judgments',
            'Family Judgment Receipts'      => 'Judgment Receipts',
            'Family Close Case'             => 'Close Case',
            'Family Return File'            => 'Return Civil File',
            'Family Enforcement'            => 'Enforcement',
            'Family Cases'                  => 'Appeal',
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
            'Family Case Registration', 'Family Case Handover', 'Family Case Handover Approval',
            'Family Case Assignment', 'Family Hearings', 'Family Judgments', 'Family Judgment Receipts',
            'Family Close Case', 'Family Return File', 'Family Enforcement', 'Family Cases',
        ];

        $ids = DB::table('permissions')->whereIn('module', $modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
