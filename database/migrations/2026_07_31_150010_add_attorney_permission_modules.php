<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fresh permission modules for the Attorney General Case Management System
     * (AGCMS) — a new subsystem, not a clone of an existing module, so there's
     * nothing to mirror role-grants from. Rows are created ungranted; Super
     * Admin (no group_id) already sees everything via User::hasPermission()'s
     * no-group-means-full-access rule. New Prosecutor/Investigator/AG Admin
     * roles get these granted manually via the Role & Permission Matrix UI
     * after the build, same as every other lookup-style residual step in
     * this app.
     */
    public function up(): void
    {
        $modules = [
            'Attorney Case Registration',
            'Attorney Investigation',
            'Attorney Prosecutor Management',
            'Attorney Court Proceedings',
            'Attorney Evidence',
            'Attorney Reports',
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
            'Attorney Case Registration', 'Attorney Investigation', 'Attorney Prosecutor Management',
            'Attorney Court Proceedings', 'Attorney Evidence', 'Attorney Reports',
        ];

        $ids = DB::table('permissions')->whereIn('module', $modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
