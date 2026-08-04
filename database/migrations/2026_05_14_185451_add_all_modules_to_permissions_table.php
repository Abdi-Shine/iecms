<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $modules = [
            'Civil Case Registration' => ['view', 'create', 'edit', 'delete'],
            'Case Parties'            => ['view', 'create', 'edit', 'delete'],
            'Case Documents'          => ['view', 'create', 'edit', 'delete'],
            'Case Lawyers'            => ['view', 'create', 'edit', 'delete'],
            'Lawyer Registry'         => ['view', 'create', 'edit', 'delete'],
            'Hearings'                => ['view', 'create', 'edit', 'delete'],
            'Judgments'               => ['view', 'create', 'edit', 'delete'],
            'Finance'                 => ['view', 'create', 'edit', 'delete'],
            'Enforcement'             => ['view', 'create', 'edit', 'delete'],
            'Case Assignment'         => ['view', 'create', 'edit', 'delete'],
            'Case Handover'           => ['view', 'create', 'edit', 'delete'],
            'Case Types'              => ['view', 'create', 'edit', 'delete'],
            'Case Categories'         => ['view', 'create', 'edit', 'delete'],
            'State & Region'          => ['view', 'create', 'edit', 'delete'],
            'Status Process'          => ['view', 'create', 'edit', 'delete'],
            'Document Attachment'     => ['view', 'create', 'edit', 'delete'],
            'Close Case'              => ['view', 'manage'],
            'Return Civil File'       => ['view', 'manage'],
            'Backup & Restore'        => ['view', 'manage'],
            'Courts Integration'      => ['view', 'create', 'edit', 'delete'],
            'Transfer Approval'       => ['view'],
            'Archive'                 => ['view', 'create', 'edit', 'delete'],
        ];

        $sort = DB::table('permissions')->max('sort_order') + 1;
        $labels = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete', 'manage' => 'Manage'];
        $now = now();

        foreach ($modules as $module => $actions) {
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
            'Civil Case Registration', 'Case Parties', 'Case Documents', 'Case Lawyers',
            'Lawyer Registry', 'Hearings', 'Judgments', 'Finance', 'Enforcement',
            'Case Assignment', 'Case Handover', 'Case Types', 'Case Categories',
            'State & Region', 'Status Process', 'Document Attachment',
            'Close Case', 'Return Civil File', 'Backup & Restore',
            'Courts Integration', 'Transfer Approval', 'Archive',
        ];

        DB::table('permissions')->whereIn('module', $modules)->delete();
    }
};
