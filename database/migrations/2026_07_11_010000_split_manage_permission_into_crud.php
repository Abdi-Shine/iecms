<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Close Case", "Return Civil File", "Backup & Restore", "Appeal Close
     * Case" and "Appeal Return File" only ever had View + a single catch-all
     * "Manage" action, unlike every other module (View/Create/Edit/Delete).
     * Splits Manage into the standard three, granting them to every role
     * that currently holds Manage, then removes Manage.
     */
    public function up(): void
    {
        $modules = ['Close Case', 'Return Civil File', 'Backup & Restore', 'Appeal Close Case', 'Appeal Return File'];

        $sortOrder = (int) Permission::max('sort_order');

        foreach ($modules as $module) {
            $manage = Permission::where('module', $module)->where('action', 'manage')->first();
            if (!$manage) {
                continue;
            }

            $roleIds = $manage->roles()->pluck('roles.id');

            foreach (['create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'] as $action => $label) {
                $sortOrder++;
                $permission = Permission::create([
                    'module'       => $module,
                    'action'       => $action,
                    'display_name' => $label,
                    'sort_order'   => $sortOrder,
                ]);
                $permission->roles()->sync($roleIds);
            }

            DB::table('role_permissions')->where('permission_id', $manage->id)->delete();
            $manage->delete();
        }
    }

    public function down(): void
    {
        $modules = ['Close Case', 'Return Civil File', 'Backup & Restore', 'Appeal Close Case', 'Appeal Return File'];

        foreach ($modules as $module) {
            Permission::where('module', $module)->whereIn('action', ['create', 'edit', 'delete'])->delete();
        }

        // Note: this does not restore the removed "Manage" permission rows
        // or their role grants.
    }
};
