<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * CID's four role tiers. "Institution Admin" (shared across every
     * institution, see 2026_08_04_180002_create_institution_admin_role.php)
     * only grants the baseline Dashboard/Staff Registry/Access
     * Login/Role & Permission set — it is deliberately NOT extended here,
     * since role_permissions has no institution dimension and doing so
     * would leak CID module visibility to every other institution's
     * admins. Instead "CID Institution Admin" is a second, CID-only role
     * carrying full access to all seven CID modules, attached to the CID
     * Admins group alongside the shared "Institution Admin" role.
     *
     * Detention Center access intentionally stops at 'view' for
     * Investigator and Officer here — the spec's own role table says
     * Investigators get "Detention Center (read + referral only)", which
     * conflicts with Menu 6 describing unrestricted admission/transfer/
     * release CRUD. The agreed resolution (see conversation) is that
     * Investigators may create a New Admission only for their own
     * case's arrestee — that is a row-level authorization check the
     * Detention Center controller enforces in Phase 5, not a blanket
     * 'create' grant here.
     */
    public function up(): void
    {
        $now = now();

        $roles = [
            'CID Institution Admin' => [
                'display_name' => 'CID Institution Admin',
                'color' => '#0EA5E9',
                'grants' => $this->allActions([
                    'CID Dashboard', 'CID Investigation Workflow', 'CID Case Management',
                    'CID Evidence & Documentation', 'CID Legal Process', 'CID Detention Center', 'CID Settings',
                ]),
                'group' => 'CID Admins',
            ],
            'Investigator' => [
                'display_name' => 'Investigator',
                'color' => '#7C3AED',
                'grants' => array_merge(
                    [['module' => 'CID Dashboard', 'action' => 'view']],
                    $this->allActions([
                        'CID Investigation Workflow', 'CID Case Management',
                        'CID Evidence & Documentation', 'CID Legal Process',
                    ]),
                    [['module' => 'CID Detention Center', 'action' => 'view']],
                ),
                'group' => 'CID Investigators',
            ],
            'Officer' => [
                'display_name' => 'Officer',
                'color' => '#059669',
                'grants' => [
                    ['module' => 'CID Dashboard', 'action' => 'view'],
                    ['module' => 'CID Investigation Workflow', 'action' => 'view'],
                    ['module' => 'CID Investigation Workflow', 'action' => 'create'],
                    ['module' => 'CID Investigation Workflow', 'action' => 'edit'],
                    ['module' => 'CID Case Management', 'action' => 'view'],
                    ['module' => 'CID Case Management', 'action' => 'create'],
                    ['module' => 'CID Case Management', 'action' => 'edit'],
                    ['module' => 'CID Evidence & Documentation', 'action' => 'view'],
                    ['module' => 'CID Evidence & Documentation', 'action' => 'create'],
                    ['module' => 'CID Legal Process', 'action' => 'view'],
                    ['module' => 'CID Detention Center', 'action' => 'view'],
                ],
                'group' => 'CID Officers',
            ],
            'Other Staff (CID)' => [
                'display_name' => 'Other Staff (CID)',
                'color' => '#6B7280',
                'grants' => [
                    ['module' => 'CID Dashboard', 'action' => 'view'],
                    ['module' => 'CID Case Management', 'action' => 'view'],
                ],
                'group' => 'CID Other Staff',
            ],
        ];

        foreach ($roles as $name => $def) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $name,
                'display_name' => $def['display_name'],
                'color' => $def['color'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($def['grants'] as $grant) {
                $permissionId = DB::table('permissions')
                    ->where('module', $grant['module'])
                    ->where('action', $grant['action'])
                    ->value('id');

                if ($permissionId) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }

            $groupId = DB::table('groups')->where('name', $def['group'])->value('id');
            if ($groupId) {
                DB::table('group_roles')->insert(['group_id' => $groupId, 'role_id' => $roleId]);
            }
        }

        // Attach the shared baseline "Institution Admin" role to the CID
        // Admins group too, same as every other institution's admin group.
        $baselineRoleId = DB::table('roles')->where('name', 'Institution Admin')->value('id');
        $cidAdminsGroupId = DB::table('groups')->where('name', 'CID Admins')->value('id');
        if ($baselineRoleId && $cidAdminsGroupId) {
            DB::table('group_roles')->insert(['group_id' => $cidAdminsGroupId, 'role_id' => $baselineRoleId]);
        }
    }

    private function allActions(array $modules): array
    {
        $grants = [];
        foreach ($modules as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $grants[] = ['module' => $module, 'action' => $action];
            }
        }
        return $grants;
    }

    public function down(): void
    {
        $names = ['CID Institution Admin', 'Investigator', 'Officer', 'Other Staff (CID)'];
        $roleIds = DB::table('roles')->whereIn('name', $names)->pluck('id');

        DB::table('group_roles')->whereIn('role_id', $roleIds)->delete();
        DB::table('role_permissions')->whereIn('role_id', $roleIds)->delete();
        DB::table('roles')->whereIn('id', $roleIds)->delete();

        $baselineRoleId = DB::table('roles')->where('name', 'Institution Admin')->value('id');
        $cidAdminsGroupId = DB::table('groups')->where('name', 'CID Admins')->value('id');
        if ($baselineRoleId && $cidAdminsGroupId) {
            DB::table('group_roles')->where('group_id', $cidAdminsGroupId)->where('role_id', $baselineRoleId)->delete();
        }
    }
};
