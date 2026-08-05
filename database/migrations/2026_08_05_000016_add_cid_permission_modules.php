<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Permission modules for the CID module, one per top-level menu
     * (Dashboard, Investigation Workflow, Case Management, Evidence &
     * Documentation, Legal Process, Detention Center, Settings). Kept at
     * this granularity for now, same as Attorney's initial six modules in
     * 2026_07_31_150010_add_attorney_permission_modules.php — finer-grained
     * modules (e.g. splitting out individual sub-menus) can be added in
     * later migrations as each phase of the CID build lands, same pattern
     * Attorney followed (Attorney Case Reviews / Attorney Prosecutor
     * Assignments were added after the base six).
     *
     * User Management, Role & Permission, and Audit Logs are intentionally
     * NOT duplicated here — CID reuses the existing institution-scoped
     * "Staff Registry" / "Role & Permission" / "Audit Log" modules that
     * every institution shares.
     */
    public function up(): void
    {
        $modules = [
            'CID Dashboard',
            'CID Investigation Workflow',
            'CID Case Management',
            'CID Evidence & Documentation',
            'CID Legal Process',
            'CID Detention Center',
            'CID Settings',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];
        $labels  = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];
        $sort    = DB::table('permissions')->max('sort_order') + 1;
        $now     = now();

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                DB::table('permissions')->insert([
                    'module'       => $module,
                    'action'       => $action,
                    'display_name' => $labels[$action],
                    'sort_order'   => $sort++,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $modules = [
            'CID Dashboard', 'CID Investigation Workflow', 'CID Case Management',
            'CID Evidence & Documentation', 'CID Legal Process', 'CID Detention Center', 'CID Settings',
        ];

        $ids = DB::table('permissions')->whereIn('module', $modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
