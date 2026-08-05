@extends('admin.admin_master')
@section('page_title', 'Roles')
@section('admin_main_content')


<div x-data="{ showAdd: false, showEdit: false, editRole: {}, showImport: false, importFile: 'No file chosen' }" class="p-4 sm:p-6 w-full">

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white"
             style="background:#528CBE">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6 mt-2">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Role Management</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Define and manage system roles and their access levels</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Export CSV --}}
            <a href="{{ route('roles.export') }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition-all hover:opacity-90"
               style="border-color:#16a34a;color:#16a34a;background:rgba(22,163,74,0.06)">
                <i class="bi bi-file-earmark-arrow-down"></i> Export
            </a>
            {{-- Import CSV --}}
            <button @click="showImport = true"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition-all hover:opacity-90"
               style="border-color:#d97706;color:#d97706;background:rgba(217,119,6,0.06)">
                <i class="bi bi-file-earmark-arrow-up"></i> Import
            </button>
            <a href="{{ route('role-permission.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-grid-3x3-gap"></i> Permission Matrix
            </a>
            <button @click="showAdd = true"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-plus-circle"></i> Add New Role
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-shield text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total Roles</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['total'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">All system roles</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-shield-lock text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">System Roles</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['system'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Built-in roles</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.12)">
                <i class="bi bi-shield-plus text-xl" style="color:#10b981"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Custom Roles</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['custom'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#10b981">User-defined roles</p>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Search --}}
        <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
            <form action="{{ route('roles.index') }}" method="GET"
                  class="flex flex-wrap gap-3 items-center">
                {{-- Page size --}}
                <div class="flex items-center gap-2 text-sm text-neutral-500 font-medium">
                    <span>Show</span>
                    <select name="per_page" onchange="this.form.submit()"
                        class="px-3 py-1.5 text-sm font-semibold border border-neutral-300 rounded-full bg-white text-neutral-700
                               focus:outline-none focus:border-[#528CBE] cursor-pointer">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative flex-1 min-w-[200px]">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by role name or slug…"
                           class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-700
                                  focus:outline-none focus:border-[#528CBE] focus:ring-2 focus:ring-[#528CBE]/20 transition-all placeholder-neutral-400">
                </div>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                    style="background:#528CBE">
                    <i class="bi bi-search"></i> Search
                </button>
                @if(request()->filled('search'))
                <a href="{{ route('roles.index') }}"
                   class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
                @endif
            </form>
        </div>

        {{-- Table Title --}}
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Role Registry</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $roles->total() }} {{ Str::plural('role', $roles->total()) }}</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-10">No#</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Role ID</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Role</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Slug</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Color</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Permissions</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Type</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @php $systemNames = ['admin','judge','registrar','clerk','staff','viewer']; @endphp
                    @forelse($roles as $index => $role)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="text-xs font-bold text-neutral-400">{{ str_pad($roles->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-4 py-4">
                            @if($role->role_id)
                                <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg"
                                      style="background:rgba(82,140,190,0.1);color:#3D78AB">{{ $role->role_id }}</span>
                            @else
                                <span class="text-xs text-neutral-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                     style="background:{{ $role->color }}">
                                    {{ strtoupper(substr($role->display_name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-neutral-800">{{ $role->display_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-neutral-100 text-neutral-600">
                                {{ $role->name }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded-full border-2 border-white shadow-sm" style="background:{{ $role->color }}"></div>
                                <span class="font-mono text-xs text-neutral-500">{{ $role->color }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold text-white"
                                  style="background:{{ $role->color }}">
                                <i class="bi bi-shield-check text-[10px]"></i>
                                {{ $role->permissions_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if(in_array($role->name, $systemNames))
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-600">System</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600">Custom</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    @click="editRole = {{ json_encode(['id' => $role->id, 'role_id' => $role->role_id, 'name' => $role->name, 'display_name' => $role->display_name, 'color' => $role->color]) }}; showEdit = true"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                    onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                    onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </button>
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                      onsubmit="confirmDelete(event, this)">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                        onmouseover="this.style.background='#DC2626';this.style.borderColor='#DC2626';this.style.color='white'"
                                        onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                        <i class="bi bi-trash3 text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                    <i class="bi bi-shield text-2xl" style="color:#528CBE"></i>
                                </div>
                                <p class="text-neutral-400 font-medium text-sm">No roles found.</p>
                                <button @click="showAdd = true"
                                    class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                                    style="background:#528CBE">Add First Role</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $roles->links() }}
        </div>
    </div>

    {{-- ── ADD MODAL ── --}}
    <div x-show="showAdd"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
         @keydown.escape.window="showAdd = false">

        <div class="bg-white flex flex-col overflow-hidden w-full"
             style="max-width:480px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
             @click.outside="showAdd = false">

            <div class="flex items-center justify-between flex-shrink-0"
                 style="background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-shield-plus" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Add New Role</h2>
                </div>
                <button @click="showAdd = false"
                    style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                    onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                    <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                </button>
            </div>

            <div style="padding:1.5rem 1.75rem">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div style="display:grid;gap:1rem;margin-bottom:1.25rem">
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Role ID</label>
                            <div style="position:relative">
                                <input type="text" value="{{ $nextRoleId }}" readonly tabindex="-1"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;font-family:monospace;font-weight:700;border:1.5px solid #d1d5db;border-radius:.625rem;background:#f3f4f6;color:#6b7280;outline:none;box-sizing:border-box;cursor:not-allowed">
                                <i class="bi bi-lock" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                            <p style="font-size:.68rem;color:#9ca3af;margin:.3rem 0 0">Auto-generated, sequential.</p>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Display Name <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <input type="text" name="display_name" id="addDisplayName" required maxlength="100"
                                       placeholder="e.g. Court Manager"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-person" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Role Slug <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <input type="text" name="name" id="addSlug" required maxlength="50"
                                       placeholder="e.g. court_manager" pattern="[a-zA-Z0-9_\-]+"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;font-family:monospace;font-weight:700;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-hash" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                            <p style="font-size:.68rem;color:#9ca3af;margin:.3rem 0 0">Lowercase, underscores only. Auto-filled from name.</p>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Badge Color <span style="color:#ef4444">*</span></label>
                            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
                                <input type="color" name="color" id="addColor" value="#528CBE"
                                       style="width:42px;height:36px;border-radius:.5rem;border:1.5px solid #e5e7eb;cursor:pointer;padding:2px">
                                @foreach(['#528CBE','#F0B43C','#10b981','#7C3AED','#ef4444','#0891B2','#6B7280','#f97316'] as $c)
                                <button type="button" onclick="document.getElementById('addColor').value='{{ $c }}'"
                                        style="width:24px;height:24px;border-radius:50%;background:{{ $c }};border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.15);cursor:pointer;transition:transform .15s"
                                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                        <button type="button" @click="showAdd = false"
                            style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">Cancel</button>
                        <button type="submit"
                            style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(82,140,190,.4)"
                            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                            <i class="bi bi-check-circle-fill"></i> Save Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── EDIT MODAL ── --}}
    <div x-show="showEdit"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
         @keydown.escape.window="showEdit = false">

        <div class="bg-white flex flex-col overflow-hidden w-full"
             style="max-width:480px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
             @click.outside="showEdit = false">

            <div class="flex items-center justify-between flex-shrink-0"
                 style="background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-pencil-square" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Edit Role</h2>
                        <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0" x-text="editRole.display_name"></p>
                    </div>
                </div>
                <button @click="showEdit = false"
                    style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                    onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                    <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                </button>
            </div>

            <div style="padding:1.5rem 1.75rem">
                <form :action="`/roles/${editRole.id}`" method="POST">
                    @csrf @method('PUT')
                    <div style="display:grid;gap:1rem;margin-bottom:1.25rem">
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Role ID <span style="font-size:.55rem;color:#9ca3af;background:#f3f4f6;padding:.1rem .35rem;border-radius:.2rem;margin-left:.2rem;font-weight:700">LOCKED</span></label>
                            <div style="position:relative">
                                <input type="text" :value="editRole.role_id || '—'" disabled
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;font-family:monospace;font-weight:700;border:1.5px solid #e5e7eb;border-radius:.625rem;background:#f9fafb;color:#9ca3af;outline:none;box-sizing:border-box;cursor:not-allowed">
                                <i class="bi bi-lock" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#d1d5db;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Role Slug <span style="font-size:.55rem;color:#9ca3af;background:#f3f4f6;padding:.1rem .35rem;border-radius:.2rem;margin-left:.2rem;font-weight:700">LOCKED</span></label>
                            <div style="position:relative">
                                <input type="text" :value="editRole.name" disabled
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #e5e7eb;border-radius:.625rem;background:#f9fafb;color:#9ca3af;outline:none;box-sizing:border-box;font-family:monospace;font-weight:700;cursor:not-allowed">
                                <i class="bi bi-lock" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#d1d5db;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Display Name <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <input type="text" name="display_name" required maxlength="100"
                                       :value="editRole.display_name"
                                       style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                       onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                       onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <i class="bi bi-person" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Badge Color <span style="color:#ef4444">*</span></label>
                            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
                                <input type="color" name="color" id="editColor"
                                       x-effect="$el.value = editRole.color || '#528CBE'"
                                       style="width:42px;height:36px;border-radius:.5rem;border:1.5px solid #e5e7eb;cursor:pointer;padding:2px">
                                @foreach(['#528CBE','#F0B43C','#10b981','#7C3AED','#ef4444','#0891B2','#6B7280','#f97316'] as $c)
                                <button type="button" onclick="document.getElementById('editColor').value='{{ $c }}'"
                                        style="width:24px;height:24px;border-radius:50%;background:{{ $c }};border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.15);cursor:pointer;transition:transform .15s"
                                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                        <button type="button" @click="showEdit = false"
                            style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">Cancel</button>
                        <button type="submit"
                            style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(82,140,190,.4)"
                            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                            <i class="bi bi-check-circle-fill"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── IMPORT MODAL ── --}}
    <div x-show="showImport"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4"
         style="display:none;background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
         @keydown.escape.window="showImport = false">

        <div class="bg-white flex flex-col overflow-hidden w-full"
             style="max-width:480px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
             @click.outside="showImport = false">

            <div class="flex items-center justify-between flex-shrink-0"
                 style="background:#d97706;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-file-earmark-arrow-up" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Import Roles</h2>
                        <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0">Upload a CSV file to bulk-add roles</p>
                    </div>
                </div>
                <button @click="showImport = false"
                    style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                    onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                    <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                </button>
            </div>

            <div style="padding:1.5rem 1.75rem">
                <div class="flex items-start gap-3 mb-5 p-3 rounded-xl" style="background:rgba(217,119,6,0.07);border:1px solid rgba(217,119,6,0.2)">
                    <i class="bi bi-info-circle-fill mt-0.5 flex-shrink-0" style="color:#d97706"></i>
                    <div style="font-size:.78rem;color:#92400e;line-height:1.5">
                        CSV columns (in order):<br>
                        <code style="font-size:.72rem;background:rgba(0,0,0,.06);padding:1px 4px;border-radius:3px">role_id, name, display_name, color</code><br>
                        <span style="color:#b45309">Leave role_id or color empty to use defaults. Duplicate name (slug) rows will be skipped.</span>
                    </div>
                </div>

                <form action="{{ route('roles.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom:1.25rem">
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.5rem">
                            CSV File <span style="color:#ef4444">*</span>
                        </label>
                        <label for="import_role_csv"
                               style="display:flex;align-items:center;justify-content:center;gap:.6rem;height:80px;border:2px dashed #d97706;border-radius:.75rem;background:rgba(217,119,6,0.04);cursor:pointer;transition:background .15s"
                               onmouseover="this.style.background='rgba(217,119,6,0.09)'" onmouseout="this.style.background='rgba(217,119,6,0.04)'">
                            <i class="bi bi-cloud-arrow-up" style="font-size:1.4rem;color:#d97706"></i>
                            <span style="font-size:.82rem;font-weight:600;color:#d97706"
                                  x-text="importFile === 'No file chosen' ? 'Click to choose CSV file' : importFile"></span>
                        </label>
                        <input type="file" id="import_role_csv" name="csv_file" accept=".csv,.txt" class="sr-only" required
                               @change="importFile = $event.target.files[0]?.name || 'No file chosen'">
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:.875rem;border-top:1.5px solid #f3f4f6">
                        <button type="button" @click="showImport = false"
                            style="padding:.6rem 1.4rem;font-size:.8rem;font-weight:700;color:#6b7280;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            Cancel
                        </button>
                        <button type="submit"
                            style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.8rem;font-weight:700;color:white;background:#d97706;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(217,119,6,.3)"
                            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                            <i class="bi bi-upload"></i> Import Roles
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('addDisplayName').addEventListener('input', function () {
    const slug = document.getElementById('addSlug');
    if (!slug.dataset.edited) {
        slug.value = this.value.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
    }
});
document.getElementById('addSlug').addEventListener('input', function () {
    this.dataset.edited = '1';
});
</script>

@endsection
