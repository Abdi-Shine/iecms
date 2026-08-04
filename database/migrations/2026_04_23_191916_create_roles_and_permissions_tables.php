<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_id', 30)->nullable()->unique();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->string('color', 20)->default('#528CBE');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module', 60);
            $table->string('action', 60);
            $table->string('display_name', 100);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        // --- Seed Roles ---
        $roles = [
            ['name' => 'admin',     'display_name' => 'Administrator',    'color' => '#528CBE'],
            ['name' => 'judge',     'display_name' => 'Judge',            'color' => '#7C3AED'],
            ['name' => 'registrar', 'display_name' => 'Registrar',        'color' => '#0891B2'],
            ['name' => 'clerk',     'display_name' => 'Clerk',            'color' => '#F0B43C'],
            ['name' => 'staff',     'display_name' => 'General Staff',    'color' => '#6B7280'],
            ['name' => 'viewer',    'display_name' => 'Read-Only Viewer', 'color' => '#9CA3AF'],
        ];
        foreach ($roles as $r) {
            DB::table('roles')->insert(array_merge($r, ['created_at' => now(), 'updated_at' => now()]));
        }

        // --- Seed Permissions ---
        $modules = [
            'Dashboard'         => ['view'],
            'Staff Registry'    => ['view', 'create', 'edit', 'delete'],
            'Positions'         => ['view', 'create', 'edit', 'delete'],
            'Judicial Units'    => ['view', 'create', 'edit', 'delete'],
            'Access Login'      => ['view', 'manage'],
            'Role & Permission' => ['view', 'manage'],
            'Audit Logs'        => ['view'],
        ];
        $sort = 1;
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                DB::table('permissions')->insert([
                    'module'       => $module,
                    'action'       => $action,
                    'display_name' => ucfirst($action),
                    'sort_order'   => $sort++,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        // Admin gets all permissions
        $adminId = DB::table('roles')->where('name', 'admin')->value('id');
        foreach (DB::table('permissions')->pluck('id') as $pid) {
            DB::table('role_permissions')->insert(['role_id' => $adminId, 'permission_id' => $pid]);
        }

        // Viewer gets all "view" permissions
        $viewerId = DB::table('roles')->where('name', 'viewer')->value('id');
        foreach (DB::table('permissions')->where('action', 'view')->pluck('id') as $pid) {
            DB::table('role_permissions')->insert(['role_id' => $viewerId, 'permission_id' => $pid]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
