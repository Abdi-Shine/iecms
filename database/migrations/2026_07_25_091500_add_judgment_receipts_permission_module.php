<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * "Qatay Xukunka Dacwada" (Judgment Receipts) was bundled under the
     * general "Judgments" permission, so it couldn't be granted or
     * restricted on its own in the Role & Permission Matrix — same class of
     * bug already fixed for Case Handover Approval in
     * 2026_07_11_020000_add_handover_approval_permission_module. This gives
     * it (and its Appeal equivalent) a dedicated module, mirroring whichever
     * roles currently hold the corresponding "Judgments" module so nobody
     * loses access.
     */
    public function up(): void
    {
        $sortOrder = (int) Permission::max('sort_order');

        $modules = [
            'Judgments'        => 'Judgment Receipts',
            'Appeal Judgments' => 'Appeal Judgment Receipts',
        ];

        foreach ($modules as $sourceModule => $newModule) {
            if (Permission::where('module', $newModule)->exists()) {
                continue;
            }

            $sourceRoleIds = Permission::where('module', $sourceModule)
                ->where('action', 'view')
                ->first()
                ?->roles()
                ->pluck('roles.id') ?? collect();

            foreach (['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'] as $action => $label) {
                $sortOrder++;
                $permission = Permission::create([
                    'module'       => $newModule,
                    'action'       => $action,
                    'display_name' => $label,
                    'sort_order'   => $sortOrder,
                ]);

                if ($action === 'view') {
                    $permission->roles()->sync($sourceRoleIds);
                }
            }
        }
    }

    public function down(): void
    {
        Permission::whereIn('module', ['Judgment Receipts', 'Appeal Judgment Receipts'])->delete();
    }
};
