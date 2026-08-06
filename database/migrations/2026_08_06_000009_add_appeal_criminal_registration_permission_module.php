<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Appeal Case Lawyers" is intentionally not duplicated here — Appeal
     * Criminal's lawyer-assignment routes reuse that existing module,
     * shared across every Appeal case type, same as Appeal Civil does.
     *
     * Granted to whichever roles currently hold Appeal Civil Registration,
     * so nobody who administers the Appeal Court loses/gains access by
     * accident when this ships.
     */
    public function up(): void
    {
        $actions = ['view', 'create', 'edit', 'delete'];
        $labels  = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];
        $sort    = DB::table('permissions')->max('sort_order') + 1;
        $now     = now();

        foreach ($actions as $action) {
            $newId = DB::table('permissions')->insertGetId([
                'module'       => 'Appeal Criminal Registration',
                'action'       => $action,
                'display_name' => $labels[$action],
                'sort_order'   => $sort++,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            $mirrorId = DB::table('permissions')
                ->where('module', 'Appeal Civil Registration')
                ->where('action', $action)
                ->value('id');

            if ($mirrorId) {
                $roleIds = DB::table('role_permissions')->where('permission_id', $mirrorId)->pluck('role_id');
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
        $ids = DB::table('permissions')->where('module', 'Appeal Criminal Registration')->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->where('module', 'Appeal Criminal Registration')->delete();
    }
};
