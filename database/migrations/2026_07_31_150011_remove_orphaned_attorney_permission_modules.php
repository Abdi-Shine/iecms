<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Investigation, Prosecutor Management, Court Proceedings, Evidence, and
     * Reports AGCMS modules were removed (controllers/views/routes deleted),
     * leaving only case registration. These permission rows are now orphaned —
     * no route or controller checks them anymore — so they're removed here to
     * stop them cluttering the Role & Permission Matrix UI. 'Attorney Case
     * Registration' is untouched since it still gates the AGCMS dashboard route.
     */
    private array $modules = [
        'Attorney Investigation',
        'Attorney Prosecutor Management',
        'Attorney Court Proceedings',
        'Attorney Evidence',
        'Attorney Reports',
    ];

    public function up(): void
    {
        $ids = DB::table('permissions')->whereIn('module', $this->modules)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('module', $this->modules)->delete();
    }

    public function down(): void
    {
        $actions = ['view', 'create', 'edit', 'delete'];
        $labels  = ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'];
        $sort    = DB::table('permissions')->max('sort_order') + 1;
        $now     = now();

        foreach ($this->modules as $module) {
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
};
