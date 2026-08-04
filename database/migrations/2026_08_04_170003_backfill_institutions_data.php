<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Classifies by name/permission rather than hardcoded IDs, since group
     * and role IDs are not guaranteed to match across environments (seed
     * order has diverged between local and production in this app before).
     */
    public function up(): void
    {
        $now = now();

        $courtsId = DB::table('institutions')->insertGetId([
            'name' => 'Courts', 'slug' => 'courts', 'type' => 'court',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        $agoId = DB::table('institutions')->insertGetId([
            'name' => "Attorney General's Office", 'slug' => 'ago', 'type' => 'ago',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // "Attorney Dashboard" is a broadly-granted shared landing-page
        // permission, not a signal of real AGO case-management access —
        // several Court roles hold it too. Only substantive Attorney
        // modules count toward AGO classification.
        $agoRoleIds = DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('permissions.module', 'like', 'Attorney%')
            ->where('permissions.module', '!=', 'Attorney Dashboard')
            ->pluck('role_permissions.role_id')
            ->unique();

        $agoGroupIdsByRole = DB::table('group_roles')
            ->whereIn('role_id', $agoRoleIds)
            ->pluck('group_id');

        $agoGroupIdsByName = DB::table('groups')
            ->whereIn('name', ['Kaalinta AGO', 'kuxigeen_xeerilaaliye'])
            ->pluck('id');

        $agoGroupIds = $agoGroupIdsByRole->merge($agoGroupIdsByName)->unique();

        DB::table('groups')->whereIn('id', $agoGroupIds)->update(['institution_id' => $agoId]);
        DB::table('groups')->whereNotIn('id', $agoGroupIds)->update(['institution_id' => $courtsId]);

        DB::statement('
            UPDATE users
            JOIN groups ON groups.id = users.group_id
            SET users.institution_id = groups.institution_id
            WHERE users.group_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        DB::table('users')->update(['institution_id' => null]);
        DB::table('groups')->update(['institution_id' => null]);
        DB::table('institutions')->whereIn('slug', ['courts', 'ago'])->delete();
    }
};
