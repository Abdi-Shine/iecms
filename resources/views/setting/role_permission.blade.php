@extends('admin.admin_master')
@section('page_title', 'Role & Permission')
@section('admin_main_content')

@php
$icons = ['view'=>'bi-eye','create'=>'bi-plus-circle','edit'=>'bi-pencil','delete'=>'bi-trash3','manage'=>'bi-sliders'];

// Build group lookup: role_id => group
$roleGroupMap = [];
foreach ($groups as $group) {
    foreach ($group->roles as $gr) {
        $roleGroupMap[$gr->id] = $group;
    }
}
// Roles that belong to no group
$ungroupedRoles = $roles->filter(fn($r) => !isset($roleGroupMap[$r->id]));
$selectedGroup  = $roleGroupMap[$selectedRole->id] ?? null;
@endphp

<div class="p-4 sm:p-6 w-full">

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-5 px-4 py-3 rounded-xl text-xs font-semibold flex items-center gap-2"
         style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:#059669">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Role & Permission Matrix</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Select a role, then assign its module-level permissions</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('groups.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-collection"></i> Manage Groups
            </a>
            <a href="{{ route('roles.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-shield"></i> Manage Roles
            </a>
        </div>
    </div>

    {{-- Role Selector --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-5 mb-5 flex flex-wrap items-center gap-4">
        <form action="{{ route('role-permission.index') }}" method="GET" class="flex items-center gap-3">
            <label class="text-xs font-black uppercase tracking-widest text-neutral-500">Role</label>
            <select name="role" onchange="this.form.submit()"
                class="px-4 py-2.5 text-sm font-semibold border border-neutral-300 rounded-xl bg-white text-neutral-700
                       focus:outline-none focus:border-[#528CBE] cursor-pointer min-w-[260px]">
                @foreach($groups as $group)
                    @if($group->roles->count())
                    <optgroup label="{{ $group->name }}">
                        @foreach($group->roles as $role)
                            <option value="{{ $role->id }}" {{ $selectedRole->id === $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </optgroup>
                    @endif
                @endforeach
                @if($ungroupedRoles->count())
                    <optgroup label="Ungrouped">
                        @foreach($ungroupedRoles as $role)
                            <option value="{{ $role->id }}" {{ $selectedRole->id === $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </optgroup>
                @endif
            </select>
        </form>

        <div class="flex items-center gap-3 ml-auto">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-black flex-shrink-0"
                 style="background:{{ $selectedRole->color }}">
                {{ strtoupper(substr($selectedRole->display_name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-bold text-neutral-800 leading-tight">{{ $selectedRole->display_name }}</p>
                <p class="text-[11px] text-neutral-400">
                    {{ $selectedGroup->name ?? 'Ungrouped' }} &middot; {{ $selectedRole->permissions->count() }} permission{{ $selectedRole->permissions->count() === 1 ? '' : 's' }} granted
                </p>
            </div>
        </div>
    </div>

    {{-- Permission Checklist Form --}}
    <form action="{{ route('role-permission.update') }}" method="POST" id="permForm">
        @csrf
        <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">

        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

            <div class="px-6 py-4 flex items-center justify-between border-b border-neutral-100">
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">
                    Permissions for {{ $selectedRole->display_name }}
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleAll(true)"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                        Select All
                    </button>
                    <button type="button" onclick="toggleAll(false)"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition">
                        Clear All
                    </button>
                </div>
            </div>

            <div class="divide-y divide-neutral-100">
                @foreach($permissions as $module => $perms)
                <div class="px-6 py-4">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="bi bi-grid-3x3-gap text-xs" style="color:#528CBE"></i>
                        <span class="text-[11px] font-black uppercase tracking-widest text-neutral-500">{{ $module }}</span>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @foreach($perms as $perm)
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-neutral-100 hover:bg-blue-50/40 cursor-pointer transition-colors min-w-[160px]">
                            <input type="checkbox"
                                   name="permissions[{{ $selectedRole->id }}][]"
                                   value="{{ $perm->id }}"
                                   class="cb perm-cb w-4 h-4 rounded cursor-pointer"
                                   style="accent-color:{{ $selectedRole->color }}"
                                   {{ $selectedRole->permissions->contains($perm->id) ? 'checked' : '' }}>
                            <span class="text-xs font-semibold text-neutral-600 flex items-center gap-1.5">
                                <i class="bi {{ $icons[$perm->action] ?? 'bi-dot' }} text-neutral-400 text-xs"></i>
                                {{ ucfirst($perm->action) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-100 flex items-center justify-between"
                 style="background:rgba(249,250,251,0.8)">
                <p class="text-xs text-neutral-400 font-semibold">
                    <span class="font-black text-neutral-600">{{ $permissions->flatten()->count() }}</span> total permissions available
                </p>
                <button type="submit"
                        class="flex items-center gap-2 px-8 py-2.5 text-white font-bold rounded-xl text-sm transition hover:opacity-90"
                        style="background:#528CBE;box-shadow:0 4px 14px rgba(82,140,190,0.35)">
                    <i class="bi bi-save-fill"></i> Save Permissions
                </button>
            </div>
        </div>
    </form>

</div>

<script>
function toggleAll(state) {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = state);
}

document.getElementById('permForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const roleId  = document.querySelector('input[name="role_id"]').value;
    const permIds = [...document.querySelectorAll('.perm-cb:checked')].map(cb => parseInt(cb.value, 10));

    const btn  = this.querySelector('button[type="submit"]');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ role_id: roleId, permissions: { [roleId]: permIds } }),
    })
    .then(r => r.json())
    .then(data => { if (data.redirect) window.location.href = data.redirect; })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = orig;
        alert('Save failed. Please try again.');
    });
});
</script>

@endsection
