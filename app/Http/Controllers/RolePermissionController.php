<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    // ── Roles list ──────────────────────────────────────────────────
    public function rolesIndex(Request $request)
    {
        $query = Role::withCount('permissions');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('display_name', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        $systemNames = ['admin', 'judge', 'registrar', 'clerk', 'staff', 'viewer'];
        $stats = [
            'total'  => (clone $query)->count(),
            'system' => (clone $query)->whereIn('name', $systemNames)->count(),
            'custom' => (clone $query)->whereNotIn('name', $systemNames)->count(),
        ];

        $perPage = $this->resolvePerPage($request);

        $roles = $query->orderBy('id')->paginate($perPage)->withQueryString();
        $nextRoleId = $this->generateNextRoleId();

        return view('Courts.setting.role_view', compact('roles', 'stats', 'nextRoleId'));
    }

    /**
     * Generate the next sequential Role ID (e.g. RID-0001).
     */
    private function generateNextRoleId(): string
    {
        $prefix = 'RID-';

        $lastNumber = Role::where('role_id', 'like', $prefix . '%')
            ->lockForUpdate()
            ->pluck('role_id')
            ->map(fn ($roleId) => (int) substr($roleId, strlen($prefix)))
            ->max();

        return $prefix . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }

    // ── Create role ─────────────────────────────────────────────────
    public function storeRole(Request $request)
    {
        $request->validate([
            'display_name' => 'required|max:100',
            'name'         => 'required|alpha_dash|max:50|unique:roles,name',
            'color'        => 'required',
        ]);

        \DB::transaction(function () use ($request) {
            Role::create(array_merge(
                $request->only('name', 'display_name', 'color'),
                ['role_id' => $this->generateNextRoleId()]
            ));
        });

        return redirect()->route('roles.index')
            ->with('success', 'Role "' . $request->display_name . '" created successfully.');
    }

    // ── Update role ─────────────────────────────────────────────────
    public function updateRole(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'display_name' => 'required|max:100',
            'color'        => 'required',
        ]);

        $role->update($request->only('display_name', 'color'));

        return redirect()->route('roles.index')
            ->with('success', 'Role "' . $role->display_name . '" updated successfully.');
    }

    // ── Delete role ─────────────────────────────────────────────────
    public function destroyRole(string $id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    // ── Export roles ────────────────────────────────────────────────
    public function exportRoles()
    {
        $roles = Role::orderBy('id')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="roles_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($roles) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['role_id', 'name', 'display_name', 'color']);
            foreach ($roles as $r) {
                fputcsv($handle, [$r->role_id ?? '', $r->name, $r->display_name, $r->color]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    // ── Import roles ────────────────────────────────────────────────
    public function importRoles(\Illuminate\Http\Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        $handle   = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header   = fgetcsv($handle);
        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) { $skipped++; continue; }
            [$roleId, $name, $displayName, $color] = array_pad($row, 4, '');
            $name = trim($name);
            if (!$name) { $skipped++; continue; }

            if (Role::where('name', $name)->exists()) { $skipped++; continue; }

            $rid = trim($roleId) ?: null;
            if ($rid && Role::where('role_id', $rid)->exists()) { $skipped++; continue; }

            Role::create([
                'role_id'      => trim($roleId) ?: null,
                'name'         => $name,
                'display_name' => trim($displayName) ?: $name,
                'color'        => trim($color) ?: '#528CBE',
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('roles.index')
            ->with('success', "Import complete: {$imported} added, {$skipped} skipped.");
    }

    // ── Permission matrix ────────────────────────────────────────────
    public function index(Request $request)
    {
        $roles       = Role::with('permissions')->orderBy('id')->get();
        $permissions = Permission::orderBy('sort_order')->get()->groupBy('module');
        $groups      = \App\Models\Group::with('roles.permissions')->where('status', 'active')->orderBy('name')->get();

        $selectedRole = $roles->firstWhere('id', (int) $request->get('role')) ?? $roles->first();

        return view('Courts.setting.role_permission', compact('roles', 'permissions', 'groups', 'selectedRole'));
    }

    public function update(Request $request)
    {
        $permissions = $request->isJson()
            ? ($request->json('permissions') ?? [])
            : ($request->input('permissions') ?? []);

        foreach ($permissions as $roleId => $permIds) {
            $role = Role::find($roleId);
            if ($role) {
                $role->permissions()->sync($permIds);
            }
        }

        $redirectParams = $request->filled('role_id') ? ['role' => $request->input('role_id')] : [];

        if ($request->expectsJson()) {
            session()->flash('success', 'Role permissions updated successfully.');
            return response()->json(['redirect' => route('role-permission.index', $redirectParams)]);
        }

        return redirect()->route('role-permission.index', $redirectParams)
            ->with('success', 'Role permissions updated successfully.');
    }
}
