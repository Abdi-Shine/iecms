<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Implements the "Recommended Admin Permissions" matrix against what
     * actually exists today. Rows with no backing feature yet (Digital
     * Signatures, Document Templates, Email & Notifications config,
     * Institution/National Reports as dedicated modules) are intentionally
     * skipped — there is nothing to grant permission to. "Suspend Users" is
     * covered by Staff Registry's existing edit action (status is an
     * editable field), not a separate action. "Manage Own/All Institutions"
     * and "View Own/All Institution Cases" are already structural
     * (BelongsToInstitution's institution scope + the is_super_admin
     * bypass), not something a role grant expresses.
     *
     * Corrections is skipped entirely — no institution row or modules
     * exist for it yet (see the Module Structure doc).
     */
    public function up(): void
    {
        $now = now();

        // ── 1. Baseline "Institution Admin": add Audit Logs view ──────────
        // Every admin type in the matrix gets some level of audit log
        // access (Institution Only / Court Only) — none show ✗ across the
        // board like Backup & Restore or Server & Security do.
        $baselineRoleId = DB::table('roles')->where('name', 'Institution Admin')->value('id');
        $this->grant($baselineRoleId, [['module' => 'Audit Logs', 'action' => 'view']], $now);

        // ── 2. AGO Institution Admin (mirrors CID Institution Admin) ──────
        $agoInstitutionId = DB::table('institutions')->where('slug', 'ago')->value('id');

        $agoAdminsGroupId = DB::table('groups')->insertGetId([
            'name'           => 'AGO Admins',
            'description'    => "AGO Admins group for the Attorney General's Office",
            'institution_id' => $agoInstitutionId,
            'color'          => '#0EA5E9',
            'status'         => 'active',
            'addedBy'        => 'System',
            'addedDate'      => $now->format('Y-m-d'),
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        $agoRoleId = DB::table('roles')->insertGetId([
            'name' => 'AGO Institution Admin', 'display_name' => 'AGO Institution Admin',
            'color' => '#0EA5E9', 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->grantAllActions($agoRoleId, [
            'Attorney Dashboard', 'Attorney Case Registration', 'Attorney Case Reviews',
            'Attorney Prosecutor Assignments', 'Attorney Departments',
        ], $now);

        DB::table('group_roles')->insert(['group_id' => $agoAdminsGroupId, 'role_id' => $agoRoleId]);
        if ($baselineRoleId) {
            DB::table('group_roles')->insert(['group_id' => $agoAdminsGroupId, 'role_id' => $baselineRoleId]);
        }

        // ── 3. Court tier admin roles ──────────────────────────────────────
        // Courts is still one shared institution (District/Regional/Appeal/
        // Supreme aren't separate institutions yet), so unlike AGO/CID these
        // aren't attached to a dedicated group here — there isn't one. They
        // exist as role definitions to assign via Role & Permission once a
        // court's tier determines who gets which, same as any other role.
        $districtCourtModules = [
            'Civil Case Registration', 'Case Assignment', 'Case Handover', 'Case Handover Approval',
            'Case Lawyers', 'Case Parties', 'Case Documents', 'Hearings', 'Judgments', 'Judgment Receipts',
            'Close Case', 'Return Civil File', 'Enforcement', 'Appeal',
            'Criminal Case Registration', 'Criminal Case Assignment', 'Criminal Case Handover',
            'Criminal Case Handover Approval', 'Criminal Case Lawyers', 'Criminal Case Parties',
            'Criminal Case Documents', 'Criminal Hearings', 'Criminal Judgments', 'Criminal Judgment Receipts',
            'Criminal Close Case', 'Criminal Return File', 'Criminal Enforcement', 'Criminal Cases',
            'Family Case Registration', 'Family Case Assignment', 'Family Case Handover',
            'Family Case Handover Approval', 'Family Case Lawyers', 'Family Case Parties',
            'Family Case Documents', 'Family Hearings', 'Family Judgments', 'Family Judgment Receipts',
            'Family Close Case', 'Family Return File', 'Family Enforcement', 'Family Cases',
            'Execution Case Registration', 'Execution Case Assignment', 'Execution Case Handover',
            'Execution Case Handover Approval', 'Execution Case Lawyers', 'Execution Case Parties',
            'Execution Case Documents', 'Execution Hearings', 'Execution Judgments', 'Execution Judgment Receipts',
            'Execution Close Case', 'Execution Return File', 'Execution Enforcement', 'Execution Cases',
        ];

        $districtRoleId = DB::table('roles')->insertGetId([
            'name' => 'District Court Admin', 'display_name' => 'District Court Admin',
            'color' => '#528CBE', 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->grantAllActions($districtRoleId, $districtCourtModules, $now);

        // Regional reuses District's exact modules (Module Structure doc,
        // Option A) — same grant set, no Regional-prefixed modules exist.
        $regionalRoleId = DB::table('roles')->insertGetId([
            'name' => 'Regional Court Admin', 'display_name' => 'Regional Court Admin',
            'color' => '#528CBE', 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->grantAllActions($regionalRoleId, $districtCourtModules, $now);

        // Appeal Court Admin: populate the existing empty "appeal_admin_"
        // role created earlier via the Role registry UI, rather than
        // creating a duplicate.
        $appealRoleId = DB::table('roles')->where('name', 'appeal_admin_')->value('id');
        if ($appealRoleId) {
            DB::table('roles')->where('id', $appealRoleId)->update(['display_name' => 'Appeal Court Admin', 'updated_at' => $now]);
        } else {
            $appealRoleId = DB::table('roles')->insertGetId([
                'name' => 'Appeal Court Admin', 'display_name' => 'Appeal Court Admin',
                'color' => '#528CBE', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        $this->grantAllActions($appealRoleId, [
            'Appeal Civil Registration', 'Appeal Criminal Registration', 'Appeal Case Assignment',
            'Appeal Case Handover', 'Appeal Case Handover Approval', 'Appeal Case Lawyers',
            'Appeal Case Parties', 'Appeal Case Documents', 'Appeal Cases', 'Appeal Close Case',
            'Appeal Enforcement', 'Appeal Hearings', 'Appeal Judgments', 'Appeal Judgment Receipts',
            'Appeal Return File',
        ], $now);

        // Supreme Court Admin: role shell only — no Supreme Court modules
        // exist yet (Module Structure doc). Gets the baseline set once
        // assigned; operational grants land when that module is built.
        DB::table('roles')->insertGetId([
            'name' => 'Supreme Court Admin', 'display_name' => 'Supreme Court Admin',
            'color' => '#528CBE', 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    private function grant(?int $roleId, array $grants, $now): void
    {
        if (!$roleId) return;

        foreach ($grants as $grant) {
            $permissionId = DB::table('permissions')
                ->where('module', $grant['module'])->where('action', $grant['action'])->value('id');
            if (!$permissionId) continue;

            $exists = DB::table('role_permissions')
                ->where('role_id', $roleId)->where('permission_id', $permissionId)->exists();
            if (!$exists) {
                DB::table('role_permissions')->insert(['role_id' => $roleId, 'permission_id' => $permissionId]);
            }
        }
    }

    private function grantAllActions(int $roleId, array $modules, $now): void
    {
        $grants = DB::table('permissions')->whereIn('module', $modules)
            ->get(['module', 'action'])
            ->map(fn ($p) => ['module' => $p->module, 'action' => $p->action])
            ->all();

        $this->grant($roleId, $grants, $now);
    }

    public function down(): void
    {
        $roleNames = ['AGO Institution Admin', 'District Court Admin', 'Regional Court Admin', 'Supreme Court Admin'];
        $roleIds = DB::table('roles')->whereIn('name', $roleNames)->pluck('id');

        DB::table('group_roles')->whereIn('role_id', $roleIds)->delete();
        DB::table('role_permissions')->whereIn('role_id', $roleIds)->delete();
        DB::table('roles')->whereIn('id', $roleIds)->delete();

        DB::table('group_roles')->whereIn('group_id', DB::table('groups')->where('name', 'AGO Admins')->pluck('id'))->delete();
        DB::table('groups')->where('name', 'AGO Admins')->delete();

        $appealRoleId = DB::table('roles')->where('name', 'appeal_admin_')->value('id');
        if ($appealRoleId) {
            DB::table('role_permissions')->where('role_id', $appealRoleId)->whereIn('permission_id',
                DB::table('permissions')->where('module', 'like', 'Appeal %')->pluck('id')
            )->delete();
            DB::table('roles')->where('id', $appealRoleId)->update(['display_name' => 'Appeal Admin']);
        }

        $baselineRoleId = DB::table('roles')->where('name', 'Institution Admin')->value('id');
        if ($baselineRoleId) {
            $auditPermId = DB::table('permissions')->where('module', 'Audit Logs')->where('action', 'view')->value('id');
            if ($auditPermId) {
                DB::table('role_permissions')->where('role_id', $baselineRoleId)->where('permission_id', $auditPermId)->delete();
            }
        }
    }
};
