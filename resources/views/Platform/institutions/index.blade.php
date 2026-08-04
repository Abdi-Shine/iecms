@extends('admin.admin_master')
@section('page_title', 'Institutions')
@section('admin_main_content')

<div x-data="{ showAdd: false, showEdit: false, editInstitution: {} }" class="p-4 sm:p-6 w-full">

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white" style="background:#528CBE">
            <i class="bi bi-check-circle-fill"></i> <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6 mt-2">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Institutions</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Register institutions and provision their Institution Admin accounts</p>
        </div>
        <button @click="showAdd = true"
            class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90"
            style="background:#528CBE">
            <i class="bi bi-plus-circle"></i> Register Institution
        </button>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-diagram-3 text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Institution Registry</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $institutions->count() }} {{ Str::plural('institution', $institutions->count()) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Name</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Type</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Groups</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Users</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Status</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($institutions as $institution)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="font-semibold text-neutral-800">{{ $institution->name }}</span>
                        </td>
                        <td class="px-4 py-4 text-neutral-500">{{ ucfirst($institution->type) }}</td>
                        <td class="px-4 py-4 text-center">{{ $institution->groups_count }}</td>
                        <td class="px-4 py-4 text-center">{{ $institution->users_count }}</td>
                        <td class="px-4 py-4 text-center">
                            @if($institution->status === 'active')
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
                                <a href="{{ route('institutions.admin.create', $institution) }}"
                                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-200 text-xs font-semibold text-neutral-600 transition-all"
                                   onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                   onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-person-plus"></i> Create Admin
                                </a>
                                <button
                                    @click="editInstitution = {{ json_encode(['id' => $institution->id, 'name' => $institution->name, 'status' => $institution->status]) }}; showEdit = true"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200 text-neutral-400 transition-all"
                                    onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                    onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background:rgba(82,140,190,0.1)">
                                    <i class="bi bi-diagram-3 text-2xl" style="color:#528CBE"></i>
                                </div>
                                <p class="text-neutral-400 font-medium text-sm">No institutions registered yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
             style="max-width:520px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25);max-height:90vh;overflow-y:auto"
             @click.outside="showAdd = false">

            <div class="flex items-center justify-between flex-shrink-0 sticky top-0"
                 style="background:#528CBE;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0;z-index:1">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-diagram-3" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Register Institution</h2>
                </div>
                <button @click="showAdd = false"
                    style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                </button>
            </div>

            <div style="padding:1.5rem 1.75rem">
                <form action="{{ route('institutions.store') }}" method="POST">
                    @csrf
                    @php
                    $fi = 'width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box';
                    $lb = 'display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem';
                    @endphp
                    <div style="display:grid;gap:1rem;margin-bottom:1.25rem">
                        <div>
                            <label style="{{ $lb }}">Institution Name <span style="color:#ef4444">*</span></label>
                            <input type="text" name="name" required maxlength="150" placeholder="e.g. Criminal Investigation Department" style="{{ $fi }}">
                        </div>
                        <div>
                            <label style="{{ $lb }}">Slug <span style="color:#ef4444">*</span></label>
                            <input type="text" name="slug" required maxlength="100" placeholder="e.g. cid" style="{{ $fi }}">
                        </div>
                        <div>
                            <label style="{{ $lb }}">Type <span style="color:#ef4444">*</span></label>
                            <select name="type" required style="{{ $fi }};cursor:pointer;appearance:none">
                                <option value="court">Court</option>
                                <option value="ago">Attorney General's Office</option>
                                <option value="cid">Criminal Investigation Department</option>
                                <option value="corrections">Correctional Services</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                        <button type="button" @click="showAdd = false"
                            style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(82,140,190,.4)">
                            <i class="bi bi-check-circle-fill"></i> Register
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
             style="max-width:480px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25);max-height:90vh;overflow-y:auto"
             @click.outside="showEdit = false">

            <div class="flex items-center justify-between flex-shrink-0 sticky top-0"
                 style="background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0;z-index:1">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-pencil-square" style="color:white;font-size:1.1rem"></i>
                    </div>
                    <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Edit Institution</h2>
                </div>
                <button @click="showEdit = false"
                    style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-x-lg" style="font-size:.85rem"></i>
                </button>
            </div>

            <div style="padding:1.5rem 1.75rem">
                <form :action="`/institutions/${editInstitution.id}`" method="POST">
                    @csrf @method('PUT')
                    @php $fi2 = 'width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box'; @endphp
                    <div style="display:grid;gap:1rem;margin-bottom:1.25rem">
                        <div>
                            <label style="{{ $lb }}">Institution Name <span style="color:#ef4444">*</span></label>
                            <input type="text" name="name" required maxlength="150" :value="editInstitution.name" style="{{ $fi2 }}">
                        </div>
                        <div>
                            <label style="{{ $lb }}">Status <span style="color:#ef4444">*</span></label>
                            <select name="status" required style="{{ $fi2 }};cursor:pointer;appearance:none">
                                <option value="active"   :selected="editInstitution.status === 'active'">Active</option>
                                <option value="inactive" :selected="editInstitution.status === 'inactive'">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                        <button type="button" @click="showEdit = false"
                            style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer">Cancel</button>
                        <button type="submit"
                            style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#F0B43C;border:none;border-radius:.625rem;cursor:pointer;box-shadow:0 4px 14px rgba(240,180,60,.4)">
                            <i class="bi bi-check-circle-fill"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
