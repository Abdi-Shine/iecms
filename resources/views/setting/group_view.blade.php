@extends('admin.admin_master')
@section('page_title', 'Group Roles')
@section('admin_main_content')

<div x-data="{ showAdd: false, showEdit: false, editGroup: {} }" class="p-4 sm:p-6 w-full">

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white" style="background:#528CBE">
            <i class="bi bi-check-circle-fill"></i> <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6 mt-2">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Group Role Management</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Bundle multiple roles into groups and assign users to them</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('roles.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-shield"></i> Manage Roles
            </a>
            <button @click="showAdd = true"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-plus-circle"></i> Add New Group
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-collection text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total Groups</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['total'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">All groups</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.12)">
                <i class="bi bi-check-circle text-xl" style="color:#10b981"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Active</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['active'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#10b981">Active groups</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(156,163,175,0.15)">
                <i class="bi bi-pause-circle text-xl" style="color:#6B7280"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Inactive</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['inactive'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#6B7280">Inactive groups</p>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Search & Filter --}}
        <form action="{{ route('groups.index') }}" method="GET" class="reg-filter">
            <div class="reg-search-wrap">
                <i class="bi bi-search reg-search-ico"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by group name…" class="reg-search-inp">
            </div>
            <select name="status" onchange="this.form.submit()" class="reg-filter-sel">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn-primary btn-sm">
                <i class="bi bi-search"></i> Search
            </button>
            @if(request()->anyFilled(['search','status']))
                <a href="{{ route('groups.index') }}" class="btn-outline btn-sm">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            @endif
        </form>

        {{-- Table Title --}}
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-table text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Group Registry</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $groups->count() }} {{ Str::plural('group', $groups->count()) }}</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-10">No#</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Group Name</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Description</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Roles Assigned</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Users</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Status</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($groups as $index => $group)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="text-xs font-bold text-neutral-400">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-black flex-shrink-0"
                                     style="background:{{ $group->color }}">
                                    {{ strtoupper(substr($group->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-semibold text-neutral-800 block">{{ $group->name }}</span>
                                    <span class="text-xs text-neutral-400">Added {{ $group->addedDate ? \Carbon\Carbon::parse($group->addedDate)->format('d/m/Y') : '—' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-neutral-500 text-sm">{{ $group->description ?: '—' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($group->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold text-white"
                                          style="background:{{ $role->color }}">
                                        {{ $role->display_name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-neutral-400 italic">No roles</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold"
                                  style="background:rgba(82,140,190,0.1);color:#528CBE">
                                <i class="bi bi-people text-[10px]"></i>
                                {{ $group->users_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($group->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                    style="background:rgba(16,185,129,0.1);color:#059669">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                    style="background:rgba(156,163,175,0.15);color:#6B7280">
                                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-400 inline-block"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    @click="editGroup = {{ json_encode(['id' => $group->id, 'name' => $group->name, 'description' => $group->description, 'color' => $group->color, 'status' => $group->status, 'role_ids' => $group->roles->pluck('id')]) }}; showEdit = true"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                    onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                    onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </button>
                                <form action="{{ route('groups.destroy', $group->id) }}" method="POST"
                                      onsubmit="return confirm('Delete this group? Users assigned will be unlinked.')">
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
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                    <i class="bi bi-collection text-2xl" style="color:#528CBE"></i>
                                </div>
                                <p class="text-neutral-400 font-medium text-sm">No groups found.</p>
                                <button @click="showAdd = true"
                                    class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                                    style="background:#528CBE">Create First Group</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-neutral-100">
            <p class="text-xs text-neutral-400 font-medium">
                Showing <span class="font-bold text-neutral-600">{{ $groups->count() }}</span> {{ Str::plural('group', $groups->count()) }}
            </p>
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
             style="max-width:560px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25);max-height:90vh;overflow-y:auto"
             @click.outside="showAdd = false">

            <div class="flex items-center justify-between flex-shrink-0 sticky top-0"
                 style="background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0;z-index:1">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-collection" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Add New Group</h2>
                </div>
                <button @click="showAdd = false"
                    style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                    onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                    <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                </button>
            </div>

            <div style="padding:1.5rem 1.75rem">
                <form action="{{ route('groups.store') }}" method="POST">
                    @csrf
                    @php
                    $fi = 'width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s';
                    $lb = 'display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem';
                    $fo = "this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'";
                    $bl = "this.style.borderColor='#d1d5db';this.style.boxShadow='none'";
                    @endphp
                    <div style="display:grid;gap:1rem;margin-bottom:1.25rem">

                        <div>
                            <label style="{{ $lb }}">Group Name <span style="color:#ef4444">*</span></label>
                            <input type="text" name="name" required maxlength="100" placeholder="e.g. Court Management Team"
                                   style="{{ $fi }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                        </div>

                        <div>
                            <label style="{{ $lb }}">Description</label>
                            <textarea name="description" rows="2" placeholder="Brief description of this group…"
                                      style="{{ $fi }};resize:none" onfocus="{{ $fo }}" onblur="{{ $bl }}"></textarea>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                            <div>
                                <label style="{{ $lb }}">Badge Color <span style="color:#ef4444">*</span></label>
                                <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
                                    <input type="color" name="color" id="addColor" value="#528CBE"
                                           style="width:42px;height:36px;border-radius:.5rem;border:1.5px solid #e5e7eb;cursor:pointer;padding:2px">
                                    @foreach(['#528CBE','#F0B43C','#10b981','#7C3AED','#ef4444','#0891B2','#6B7280','#f97316'] as $c)
                                    <button type="button" onclick="document.getElementById('addColor').value='{{ $c }}'"
                                            style="width:22px;height:22px;border-radius:50%;background:{{ $c }};border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.15);cursor:pointer"
                                            onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'"></button>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label style="{{ $lb }}">Status <span style="color:#ef4444">*</span></label>
                                <select name="status" required style="{{ $fi }};cursor:pointer;appearance:none" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="{{ $lb }}">Assign Roles</label>
                            <div style="border:1.5px solid #e5e7eb;border-radius:.625rem;padding:.75rem;max-height:180px;overflow-y:auto;background:#fafafa">
                                @foreach($roles as $role)
                                <label style="display:flex;align-items:center;gap:.6rem;padding:.35rem .25rem;cursor:pointer;border-radius:.375rem"
                                       onmouseover="this.style.background='rgba(82,140,190,0.06)'" onmouseout="this.style.background=''">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                           style="width:15px;height:15px;accent-color:#528CBE;cursor:pointer;flex-shrink:0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold text-white"
                                          style="background:{{ $role->color }}">{{ $role->display_name }}</span>
                                </label>
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
                            <i class="bi bi-check-circle-fill"></i> Save Group
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
             style="max-width:560px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25);max-height:90vh;overflow-y:auto"
             @click.outside="showEdit = false">

            <div class="flex items-center justify-between flex-shrink-0 sticky top-0"
                 style="background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0;z-index:1">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-pencil-square" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <div>
                        <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Edit Group</h2>
                        <p style="color:rgba(255,255,255,.85);font-size:.75rem;margin:0" x-text="editGroup.name"></p>
                    </div>
                </div>
                <button @click="showEdit = false"
                    style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                    onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                    <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                </button>
            </div>

            <div style="padding:1.5rem 1.75rem">
                <form :action="`/groups/${editGroup.id}`" method="POST">
                    @csrf @method('PUT')
                    @php
                    $fi = 'width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s';
                    $lb = 'display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem';
                    $fo = "this.style.borderColor='#F0B43C';this.style.boxShadow='0 0 0 3px rgba(240,180,60,.15)'";
                    $bl = "this.style.borderColor='#d1d5db';this.style.boxShadow='none'";
                    @endphp
                    <div style="display:grid;gap:1rem;margin-bottom:1.25rem">

                        <div>
                            <label style="{{ $lb }}">Group Name <span style="color:#ef4444">*</span></label>
                            <input type="text" name="name" required maxlength="100"
                                   :value="editGroup.name"
                                   style="{{ $fi }}" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                        </div>

                        <div>
                            <label style="{{ $lb }}">Description</label>
                            <textarea name="description" rows="2"
                                      x-text="editGroup.description || ''"
                                      style="{{ $fi }};resize:none" onfocus="{{ $fo }}" onblur="{{ $bl }}"></textarea>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                            <div>
                                <label style="{{ $lb }}">Badge Color <span style="color:#ef4444">*</span></label>
                                <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
                                    <input type="color" name="color" id="editColor"
                                           x-effect="$el.value = editGroup.color || '#528CBE'"
                                           style="width:42px;height:36px;border-radius:.5rem;border:1.5px solid #e5e7eb;cursor:pointer;padding:2px">
                                    @foreach(['#528CBE','#F0B43C','#10b981','#7C3AED','#ef4444','#0891B2','#6B7280','#f97316'] as $c)
                                    <button type="button" onclick="document.getElementById('editColor').value='{{ $c }}'"
                                            style="width:22px;height:22px;border-radius:50%;background:{{ $c }};border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.15);cursor:pointer"
                                            onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'"></button>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label style="{{ $lb }}">Status <span style="color:#ef4444">*</span></label>
                                <select name="status" required style="{{ $fi }};cursor:pointer;appearance:none" onfocus="{{ $fo }}" onblur="{{ $bl }}">
                                    <option value="active"   :selected="editGroup.status === 'active'">Active</option>
                                    <option value="inactive" :selected="editGroup.status === 'inactive'">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="{{ $lb }}">Assign Roles</label>
                            <div style="border:1.5px solid #e5e7eb;border-radius:.625rem;padding:.75rem;max-height:180px;overflow-y:auto;background:#fafafa">
                                @foreach($roles as $role)
                                <label style="display:flex;align-items:center;gap:.6rem;padding:.35rem .25rem;cursor:pointer;border-radius:.375rem"
                                       onmouseover="this.style.background='rgba(240,180,60,0.06)'" onmouseout="this.style.background=''">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                           :checked="editGroup.role_ids && editGroup.role_ids.includes({{ $role->id }})"
                                           style="width:15px;height:15px;accent-color:#F0B43C;cursor:pointer;flex-shrink:0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold text-white"
                                          style="background:{{ $role->color }}">{{ $role->display_name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                        <button type="button" @click="showEdit = false"
                            style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">Cancel</button>
                        <button type="submit"
                            style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#F0B43C;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(240,180,60,.4)"
                            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                            <i class="bi bi-check-circle-fill"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
