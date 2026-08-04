<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Permission catalog entry for Super-Admin-only institution
     * provisioning. Super admins bypass permission checks entirely
     * (User::hasPermission), so no role needs these granted explicitly —
     * this just registers the module so CheckPermission middleware has
     * something to gate the new routes against for everyone else.
     */
    public function up(): void
    {
        $actions = [
            'view'                      => 'View',
            'manage-institutions'       => 'Manage Institutions',
            'manage-institution-admins' => 'Manage Institution Admins',
        ];
        $sort = DB::table('permissions')->max('sort_order') + 1;
        $now  = now();

        foreach ($actions as $action => $label) {
            DB::table('permissions')->insert([
                'module'       => 'Platform Administration',
                'action'       => $action,
                'display_name' => $label,
                'sort_order'   => $sort++,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->where('module', 'Platform Administration')->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->where('module', 'Platform Administration')->delete();
    }
};
