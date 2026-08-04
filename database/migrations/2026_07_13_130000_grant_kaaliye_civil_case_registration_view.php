<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Kaaliye (Clerk) could only reach the case information/history page
     * indirectly, via a route-level exception that also accepts the
     * "Hearings" permission (see the civil-registration.show route fix).
     * That workaround stays in place for any other role in the same
     * situation, but Kaaliye specifically should just hold the real
     * "Civil Case Registration: view" permission directly, since viewing a
     * case's full history is core to the clerk's job — not create/edit/
     * delete, which stay restricted to whoever actually manages
     * registrations.
     */
    public function up(): void
    {
        $permission = Permission::where('module', 'Civil Case Registration')->where('action', 'view')->first();
        $kaaliye = Role::where('name', 'Kaaliye')->first();

        if ($permission && $kaaliye) {
            $kaaliye->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    public function down(): void
    {
        $permission = Permission::where('module', 'Civil Case Registration')->where('action', 'view')->first();
        $kaaliye = Role::where('name', 'Kaaliye')->first();

        if ($permission && $kaaliye) {
            $kaaliye->permissions()->detach($permission->id);
        }
    }
};
