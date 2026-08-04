<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The "Oggolaanshaha Dhaqdhaqaaqa" (Handover Approval) page was bundled
     * under the general "Case Handover" permission, so it couldn't be
     * granted or restricted on its own in the Role & Permission Matrix. This
     * gives it (and its Appeal equivalent) a dedicated module, mirroring
     * whichever roles currently hold the corresponding "Case Handover"
     * module so nobody loses access.
     */
    public function up(): void
    {
        $sortOrder = (int) Permission::max('sort_order');

        $modules = [
            'Case Handover'        => 'Case Handover Approval',
            'Appeal Case Handover' => 'Appeal Case Handover Approval',
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

                // Only mirror "view" onto the new module's "view" — approval
                // capability shouldn't imply create/edit/delete rights.
                if ($action === 'view') {
                    $permission->roles()->sync($sourceRoleIds);
                }
            }
        }
    }

    public function down(): void
    {
        Permission::whereIn('module', ['Case Handover Approval', 'Appeal Case Handover Approval'])->delete();
    }
};
