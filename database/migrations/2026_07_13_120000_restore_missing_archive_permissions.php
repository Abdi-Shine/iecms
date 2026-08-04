<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The 2026_05_14_185451 migration was supposed to seed the "Archive"
     * module (view/create/edit/delete) but the rows never made it into the
     * database, even though it's recorded as having run — leaving Archive
     * permission-less while the sidebar section, routes, and middleware for
     * it already exist. No role could be granted a permission that didn't
     * exist, so the Archive menu was invisible to every account except
     * super-admins (users with no group, who bypass permission checks
     * entirely).
     *
     * This restores the module and grants it to:
     *  - every role that already holds every other module in the system
     *    (the established "full access" set, e.g. Sarkaalka keydka /
     *    Archive Officer), mirroring how those roles already treat every
     *    other module;
     *  - Kaaliye, view-only — the archive stamp-request routes already have
     *    a comment stating they're "accessible by Kaaliye and Archive
     *    Officer".
     */
    public function up(): void
    {
        if (Permission::where('module', 'Archive')->exists()) {
            return;
        }

        // "Full access" = holds at least one permission in every currently-
        // existing non-Archive module (mirrors how those roles already treat
        // every other module — some modules only grant a subset of actions,
        // e.g. 'Close Case' only has view/manage, so this checks module
        // coverage rather than requiring literally every action row).
        $otherModuleCount = Permission::where('module', '!=', 'Archive')->distinct()->count('module');
        $fullAccessRoleIds = DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('permissions.module', '!=', 'Archive')
            ->groupBy('role_permissions.role_id')
            ->havingRaw('COUNT(DISTINCT permissions.module) = ?', [$otherModuleCount])
            ->pluck('role_permissions.role_id');

        $kaaliyeId = Role::where('name', 'Kaaliye')->value('id');

        $sortOrder = (int) Permission::max('sort_order');

        foreach (['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'] as $action => $label) {
            $sortOrder++;
            $permission = Permission::create([
                'module'       => 'Archive',
                'action'       => $action,
                'display_name' => $label,
                'sort_order'   => $sortOrder,
            ]);

            $permission->roles()->sync($fullAccessRoleIds);

            if ($action === 'view' && $kaaliyeId && !$fullAccessRoleIds->contains($kaaliyeId)) {
                $permission->roles()->attach($kaaliyeId);
            }
        }
    }

    public function down(): void
    {
        Permission::where('module', 'Archive')->delete();
    }
};
