<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seeds the Criminal Investigation Department institution and its four
     * standard Groups (Admins, Investigators, Officers, Other Staff). The
     * 'cid' institution type already existed in the institutions.type enum
     * (unused) since 2026_08_04_170000_create_institutions_table.php.
     * Roles are attached to these groups separately in
     * 2026_08_05_000017_create_cid_roles.php, once permissions exist.
     */
    public function up(): void
    {
        $now = now();

        $institutionId = DB::table('institutions')->insertGetId([
            'name' => 'Criminal Investigation Department',
            'slug' => 'cid',
            'type' => 'cid',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $groups = [
            'CID Admins',
            'CID Investigators',
            'CID Officers',
            'CID Other Staff',
        ];

        foreach ($groups as $name) {
            DB::table('groups')->insert([
                'name' => $name,
                'description' => $name . ' group for Criminal Investigation Department',
                'institution_id' => $institutionId,
                'status' => 'active',
                'addedBy' => 'System',
                'addedDate' => $now->format('Y-m-d'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $institutionId = DB::table('institutions')->where('slug', 'cid')->value('id');

        if ($institutionId) {
            $groupIds = DB::table('groups')->where('institution_id', $institutionId)->pluck('id');
            DB::table('group_roles')->whereIn('group_id', $groupIds)->delete();
            DB::table('groups')->whereIn('id', $groupIds)->delete();
            DB::table('institutions')->where('id', $institutionId)->delete();
        }
    }
};
