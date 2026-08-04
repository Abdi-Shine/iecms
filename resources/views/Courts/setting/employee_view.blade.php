@extends('admin.admin_master')
@section('page_title', 'Employee Management')
@section('admin_main_content')


<div x-data="{ showEdit: false, showImport: false, editEmp: {}, editFile: 'No file chosen', importFile: 'No file chosen' }" class="p-4 sm:p-6 w-full">



    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Employee Management</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Manage all court staff and judicial officers</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Export CSV --}}
            <a href="{{ route('employee.export') }}"
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
            {{-- Add New --}}
            <a href="{{ route('employee.create') }}"
                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all hover:opacity-90"
                style="background:#528CBE">
                <i class="bi bi-person-plus-fill"></i> Add New Employee
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Total Staff --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-people-fill text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total Staff</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['total'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Complete workforce</p>
            </div>
        </div>

        {{-- Judges --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-shield-check text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Judges</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                    {{ $stats['judges'] }}
                </h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Judicial officers</p>
            </div>
        </div>

        {{-- Clerks --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-diagram-3 text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Clerks</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                    {{ $stats['clerks'] }}
                </h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Registry staff</p>
            </div>
        </div>

        {{-- Chiefs --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-person-badge text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Chiefs</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                    {{ $stats['chiefs'] }}
                </h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Head officers</p>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        {{-- Search & Filter --}}
        <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
            <form action="{{ route('employee.index') }}" method="GET"
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

                {{-- Search --}}
                <div class="relative flex-1 min-w-[200px]">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name, ID or email…"
                           class="w-full pl-10 pr-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50
                                  text-neutral-700 placeholder-neutral-400 focus:outline-none focus:border-[#528CBE]
                                  focus:ring-2 focus:ring-[#528CBE]/20 transition-all">
                </div>

                {{-- Court filter --}}
                <select name="unit" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           focus:outline-none focus:border-[#528CBE] min-w-[220px] cursor-pointer">
                    <option value="">All Courts</option>
                    @foreach($courts->sortBy('longName') as $court)
                        <option value="{{ $court->courtcode }}" {{ request('unit') == $court->courtcode ? 'selected' : '' }}>
                            {{ $court->longName }}
                        </option>
                    @endforeach
                </select>

                {{-- Status filter --}}
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           focus:outline-none focus:border-[#528CBE] min-w-[140px] cursor-pointer">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                {{-- Search button --}}
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                    style="background:#528CBE">
                    <i class="bi bi-search"></i> Search
                </button>

                @if(request()->anyFilled(['search','unit','status']))
                <a href="{{ route('employee.index') }}"
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
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Master Employee Registry</span>
            </div>
            <span class="text-xs text-neutral-400 font-medium">{{ $employees->total() }} {{ Str::plural('record', $employees->total()) }}</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 w-16">No#</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Full Name</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Gender</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Telephone</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Role</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Court</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Status</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Security</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($employees as $index => $employee)
                    <tr class="hover:bg-neutral-50 transition-colors">

                        {{-- No --}}
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-neutral-400">
                                {{ str_pad($employees->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>

                        {{-- Full Name --}}
                        <td class="px-6 py-4">
                            <span class="font-semibold text-neutral-800">{{ $employee->EmpName }}</span>
                        </td>

                        {{-- Gender --}}
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                {{ $employee->gender === 'Male'
                                    ? 'bg-blue-50 text-blue-600'
                                    : 'bg-pink-50 text-pink-500' }}">
                                {{ $employee->gender }}
                            </span>
                        </td>

                        {{-- Phone --}}
                        <td class="px-6 py-4 text-neutral-600 text-sm">{{ $employee->phone }}</td>

                        {{-- Position --}}
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                                  style="background:rgba(240,180,60,0.12);color:#C07E15">
                                {{ $employee->Position }}
                            </span>
                        </td>

                        {{-- Court --}}
                        <td class="px-6 py-4 text-neutral-600 text-sm font-medium">
                            {{ $employee->court->longName ?? $employee->courtID }}
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @if($employee->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="background:rgba(16,185,129,0.1);color:#059669">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="background:rgba(156,163,175,0.15);color:#6B7280">
                                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-400 inline-block"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Security --}}
                        <td class="px-6 py-4 text-center">
                            @if($employee->islogin == 1 || $employee->system_username)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="background:rgba(16,185,129,0.1);color:#059669">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                    Authorized
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="background:rgba(156,163,175,0.15);color:#6B7280">
                                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-400 inline-block"></span>
                                    No Access
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    @click="editEmp = {{ json_encode($employee) }};
                                            editEmp.DOB = '{{ $employee->DOB ? $employee->DOB->format('Y-m-d') : '' }}';
                                            editEmp.Dates = '{{ $employee->Dates ? $employee->Dates->format('Y-m-d') : '' }}';
                                            clearEditSig();
                                            showEdit = true"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200
                                           text-neutral-400 hover:text-white transition-all"
                                    style="hover:background:#528CBE"
                                    onmouseover="this.style.background='#528CBE';this.style.borderColor='#528CBE';this.style.color='white'"
                                    onmouseout="this.style.background='';this.style.borderColor='';this.style.color=''">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </button>
                                <form action="{{ route('employee.destroy', $employee->AID) }}" method="POST"
                                      onsubmit="confirmDelete(event, this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-neutral-200
                                               text-neutral-400 transition-all"
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
                        <td colspan="9" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                     style="background:rgba(82,140,190,0.1)">
                                    <i class="bi bi-people text-2xl" style="color:#528CBE"></i>
                                </div>
                                <p class="text-neutral-400 font-medium text-sm">No employees found in the registry.</p>
                                <a href="{{ route('employee.create') }}"
                                    class="mt-1 px-4 py-2 text-xs font-semibold text-white rounded-lg transition hover:opacity-90"
                                    style="background:#528CBE">
                                    Add First Employee
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $employees->links() }}
        </div>
    </div>

{{-- ═══ Edit Employee Modal ═══ --}}
<div x-show="showEdit"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[70] flex items-center justify-center p-4"
     style="background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
     @keydown.escape.window="showEdit = false">

    <div class="bg-white flex flex-col overflow-hidden w-full"
         style="max-width:780px;max-height:92vh;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         @click.outside="showEdit = false">

        {{-- Header --}}
        <div class="flex items-center justify-between flex-shrink-0" style="background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0">
            <div class="flex items-center gap-3">
                <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-center">
                    <i class="bi bi-pencil-square" style="color:white;font-size:1.1rem"></i>
                </div>
                <div>
                    <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Edit Staff Member</h2>
                    <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0" x-text="editEmp.EmpName"></p>
                </div>
            </div>
            <button @click="showEdit = false"
                style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s"
                onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                <i class="bi bi-x-lg" style="font-size:.85rem"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1" style="padding:1.5rem 1.75rem">
            <form :action="`/employee/${editEmp.AID}`" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display:flex;flex-direction:column;gap:1.25rem">

                {{-- Row 1: ID | Name | Gender --}}
                <div style="display:grid;grid-template-columns:1fr 1.5fr 1fr;gap:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Employee ID</label>
                        <div style="position:relative">
                            <input type="text" name="EmpID" x-model="editEmp.EmpID"
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                   onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-hash" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Full Name</label>
                        <div style="position:relative">
                            <input type="text" name="EmpName" x-model="editEmp.EmpName"
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                   onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-person" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Gender</label>
                        <div style="position:relative">
                            <select name="gender" x-model="editEmp.gender"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Phone | Email | POB --}}
                <div style="display:grid;grid-template-columns:1fr 1.5fr 1fr;gap:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Telephone</label>
                        <div style="position:relative">
                            <input type="text" name="phone" x-model="editEmp.phone" inputmode="numeric"
                                   @input="editEmp.phone = editEmp.phone.replace(/\D/g,'')"
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                   onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-telephone" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Email</label>
                        <div style="position:relative">
                            <input type="email" name="email" x-model="editEmp.email"
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                   onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-envelope" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Place of Birth</label>
                        <div style="position:relative">
                            <select name="POB" x-model="editEmp.POB"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <option value="Awdal">Awdal</option><option value="Bakool">Bakool</option>
                                <option value="Banaadir">Banaadir</option><option value="Bari">Bari</option>
                                <option value="Bay">Bay</option><option value="Galguduud">Galguduud</option>
                                <option value="Gedo">Gedo</option><option value="Hiiraan">Hiiraan</option>
                                <option value="Jubbada Dhexe">Jubbada Dhexe</option>
                                <option value="Jubbada Hoose">Jubbada Hoose</option>
                                <option value="Mudug">Mudug</option><option value="Nugaal">Nugaal</option>
                                <option value="Sanaag">Sanaag</option>
                                <option value="Shabeellaha Dhexe">Shabeellaha Dhexe</option>
                                <option value="Shabeellaha Hoose">Shabeellaha Hoose</option>
                                <option value="Sool">Sool</option><option value="Togdheer">Togdheer</option>
                                <option value="Woqooyi Galbeed">Woqooyi Galbeed</option>
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                </div>

                {{-- Row 3: DOB | Court | Role --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Date of Birth</label>
                        <div style="position:relative">
                            <input type="date" name="DOB" x-model="editEmp.DOB"
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                   onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-calendar3" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Court Assignment</label>
                        <div style="position:relative">
                            <select name="courtID" x-model="editEmp.courtID"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                @foreach($courts as $court)
                                    <option value="{{ $court->courtcode }}">{{ $court->longName }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Role</label>
                        <div style="position:relative">
                            <select name="Position" x-model="editEmp.Position"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                </div>

                {{-- Row 4: Status | Reg Date | Photo --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Status</label>
                        <div style="position:relative">
                            <select name="status" x-model="editEmp.status"
                                    style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                    onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <i class="bi bi-chevron-down" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.65rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Registration Date</label>
                        <div style="position:relative">
                            <input type="date" name="Dates" x-model="editEmp.Dates"
                                   style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                                   onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <i class="bi bi-calendar-check" style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);font-size:.8rem;color:#9ca3af;pointer-events:none"></i>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">Update Photo</label>
                        <label for="edit_photo"
                               style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.25rem;height:42px;border:1.5px dashed #d1d5db;border-radius:.625rem;background:#fafafa;cursor:pointer;transition:border-color .15s,background .15s"
                               onmouseover="this.style.borderColor='#528CBE';this.style.background='rgba(82,140,190,.04)'"
                               onmouseout="this.style.borderColor='#d1d5db';this.style.background='#fafafa'">
                            <span style="display:flex;align-items:center;gap:.4rem;font-size:.72rem;font-weight:700;color:#9ca3af">
                                <i class="bi bi-cloud-arrow-up" style="font-size:.9rem"></i>
                                <span x-text="editFile === 'No file chosen' ? 'Update Photo' : editFile" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
                            </span>
                        </label>
                        <input type="file" id="edit_photo" name="photo" accept="image/*" class="sr-only"
                               @change="editFile = $event.target.files[0]?.name || 'No file chosen'">
                    </div>
                </div>

                {{-- Digital Signature --}}
                <div style="border:1.5px solid #e5e7eb;border-radius:1rem;padding:1.1rem 1.25rem;background:#fafafa">

                    {{-- Header row --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem">
                        <label style="font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em">
                            Digital Signature
                        </label>
                        <template x-if="editEmp.signature">
                            <span style="font-size:.7rem;color:#10b981;font-weight:700;display:flex;align-items:center;gap:.35rem">
                                <i class="bi bi-patch-check-fill"></i> Signature on file
                            </span>
                        </template>
                    </div>

                    {{-- Existing signature preview --}}
                    <template x-if="editEmp.signature">
                        <div style="margin-bottom:.875rem;padding:.625rem;background:white;border:1.5px solid #d1d5db;border-radius:.625rem;display:flex;align-items:center;gap:.875rem">
                            <div style="width:140px;height:60px;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                                <img :src="'/' + editEmp.signature" style="max-width:100%;max-height:100%;object-fit:contain">
                            </div>
                            <div>
                                <p style="font-size:.7rem;font-weight:700;color:#374151;margin:0 0 .2rem">Current signature</p>
                                <p style="font-size:.68rem;color:#9ca3af;margin:0">Draw or upload below to replace it.</p>
                            </div>
                        </div>
                    </template>

                    {{-- Tabs --}}
                    <div style="display:flex;gap:0;border:1.5px solid #e5e7eb;border-radius:.625rem;overflow:hidden;margin-bottom:.875rem;background:white">
                        <button type="button" id="edit-tab-draw"
                            onclick="editSigTab('draw')"
                            style="flex:1;padding:.5rem;font-size:.72rem;font-weight:700;border:none;cursor:pointer;background:#528CBE;color:white;display:flex;align-items:center;justify-content:center;gap:.35rem;transition:background .15s">
                            <i class="bi bi-pen"></i> Draw Signature
                        </button>
                        <button type="button" id="edit-tab-upload"
                            onclick="editSigTab('upload')"
                            style="flex:1;padding:.5rem;font-size:.72rem;font-weight:700;border:none;cursor:pointer;background:white;color:#6b7280;display:flex;align-items:center;justify-content:center;gap:.35rem;transition:background .15s">
                            <i class="bi bi-image"></i> Upload Image
                        </button>
                    </div>

                    {{-- Draw panel --}}
                    <div id="edit-panel-draw">
                        <canvas id="edit-sig-canvas" width="460" height="100"
                                style="display:block;width:100%;height:100px;border:1.5px dashed #d1d5db;border-radius:.5rem;background:white;cursor:crosshair;touch-action:none">
                        </canvas>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.5rem">
                            <span style="font-size:.7rem;color:#9ca3af"><i class="bi bi-mouse2"></i> Draw with mouse or touch</span>
                            <div style="display:flex;gap:.4rem">
                                <button type="button" onclick="clearEditSig()"
                                    style="padding:.3rem .7rem;font-size:.7rem;font-weight:700;color:#6b7280;background:white;border:1.5px solid #e5e7eb;border-radius:.4rem;cursor:pointer">
                                    <i class="bi bi-eraser"></i> Clear
                                </button>
                                <button type="button" onclick="captureEditSig()"
                                    style="padding:.3rem .7rem;font-size:.7rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.4rem;cursor:pointer">
                                    <i class="bi bi-check2"></i> Use This
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Upload panel --}}
                    <div id="edit-panel-upload" style="display:none">
                        <label for="edit-sig-file"
                               style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;height:100px;border:2px dashed #d1d5db;border-radius:.5rem;background:white;cursor:pointer;transition:border-color .15s"
                               onmouseover="this.style.borderColor='#528CBE'" onmouseout="this.style.borderColor='#d1d5db'">
                            <i class="bi bi-cloud-arrow-up" style="font-size:1.75rem;color:#9ca3af"></i>
                            <span style="font-size:.75rem;font-weight:600;color:#6b7280">Click to choose image</span>
                            <span style="font-size:.68rem;color:#9ca3af">PNG, JPG, JPEG — white background recommended</span>
                        </label>
                        <input type="file" id="edit-sig-file" accept="image/png,image/jpeg,image/jpg"
                               style="display:none" onchange="editSigFileSelected(this)">
                        <div id="edit-sig-file-preview" style="display:none;margin-top:.5rem;padding:.5rem;background:white;border:1.5px solid #d1d5db;border-radius:.5rem;text-align:center">
                            <img id="edit-sig-file-img" style="max-height:70px;max-width:100%;object-fit:contain">
                        </div>
                    </div>

                    <p id="edit-sig-msg" style="display:none;font-size:.7rem;color:#10b981;font-weight:700;margin:.5rem 0 0">
                        <i class="bi bi-patch-check-fill"></i> Signature captured — will save with the form.
                    </p>
                    <input type="hidden" name="signature_data" id="edit-sig-data">
                </div>

                </div>{{-- end flex col --}}

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;margin-top:1.25rem;border-top:1.5px solid #f3f4f6">
                    <button type="button" @click="showEdit = false"
                        style="padding:.6rem 1.5rem;font-size:.8rem;font-weight:700;color:#6b7280;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer;text-transform:uppercase;letter-spacing:.04em;transition:background .15s"
                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        Cancel
                    </button>
                    <button type="submit"
                        style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.8rem;font-weight:700;color:white;background:#F0B43C;border:none;border-radius:.625rem;cursor:pointer;text-transform:uppercase;letter-spacing:.04em;box-shadow:0 4px 14px rgba(240,180,60,.35);transition:opacity .15s"
                        onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        <i class="bi bi-check-circle-fill"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ Import Modal ═══ --}}
<div x-show="showImport"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[70] flex items-center justify-center p-4"
     style="background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
     @keydown.escape.window="showImport = false">

    <div class="bg-white flex flex-col overflow-hidden w-full"
         style="max-width:480px;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         @click.outside="showImport = false">

        {{-- Header --}}
        <div class="flex items-center justify-between flex-shrink-0"
             style="background:#d97706;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0">
            <div class="flex items-center gap-3">
                <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-file-earmark-arrow-up" style="color:white;font-size:1.1rem"></i>
                </div>
                <div>
                    <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Import Employees</h2>
                    <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0">Upload a CSV file to bulk-add employees</p>
                </div>
            </div>
            <button @click="showImport = false"
                style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                <i class="bi bi-x-lg" style="font-size:.85rem"></i>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.5rem 1.75rem">
            {{-- Template download hint --}}
            <div class="flex items-start gap-3 mb-5 p-3 rounded-xl" style="background:rgba(217,119,6,0.07);border:1px solid rgba(217,119,6,0.2)">
                <i class="bi bi-info-circle-fill mt-0.5 flex-shrink-0" style="color:#d97706"></i>
                <div style="font-size:.78rem;color:#92400e;line-height:1.5">
                    CSV columns (in order):<br>
                    <code style="font-size:.72rem;background:rgba(0,0,0,.06);padding:1px 4px;border-radius:3px">EmpID, EmpName, gender, phone, email, DOB, POB, Position, courtID, status, Dates</code><br>
                    <span style="color:#b45309">Duplicate EmpID or email rows will be skipped.</span>
                </div>
            </div>

            <form action="{{ route('employee.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:1.25rem">
                    <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.5rem">
                        CSV File <span style="color:#ef4444">*</span>
                    </label>
                    <label for="import_csv"
                           style="display:flex;align-items:center;justify-content:center;gap:.6rem;height:80px;border:2px dashed #d97706;border-radius:.75rem;background:rgba(217,119,6,0.04);cursor:pointer;transition:background .15s"
                           onmouseover="this.style.background='rgba(217,119,6,0.09)'" onmouseout="this.style.background='rgba(217,119,6,0.04)'">
                        <i class="bi bi-cloud-arrow-up" style="font-size:1.4rem;color:#d97706"></i>
                        <span style="font-size:.82rem;font-weight:600;color:#d97706"
                              x-text="importFile === 'No file chosen' ? 'Click to choose CSV file' : importFile"></span>
                    </label>
                    <input type="file" id="import_csv" name="csv_file" accept=".csv,.txt" class="sr-only" required
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
                        <i class="bi bi-upload"></i> Import Employees
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var ctx, drawing = false;

    function getCanvas() { return document.getElementById('edit-sig-canvas'); }
    function msg(show)   { document.getElementById('edit-sig-msg').style.display = show ? 'block' : 'none'; }

    function pos(e) {
        var c = getCanvas(), r = c.getBoundingClientRect();
        var sx = c.width / r.width, sy = c.height / r.height;
        var src = e.touches ? e.touches[0] : e;
        return { x: (src.clientX - r.left) * sx, y: (src.clientY - r.top) * sy };
    }

    function bindCanvas(c) {
        c.addEventListener('mousedown',  function(e) { drawing = true; var p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        c.addEventListener('mousemove',  function(e) { if (!drawing) return; var p = pos(e); ctx.lineTo(p.x, p.y); ctx.strokeStyle = '#528CBE'; ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.stroke(); });
        c.addEventListener('mouseup',    function() { drawing = false; });
        c.addEventListener('mouseleave', function() { drawing = false; });
        c.addEventListener('touchstart', function(e) { e.preventDefault(); drawing = true; var p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }, { passive: false });
        c.addEventListener('touchmove',  function(e) { e.preventDefault(); if (!drawing) return; var p = pos(e); ctx.lineTo(p.x, p.y); ctx.strokeStyle = '#528CBE'; ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.stroke(); }, { passive: false });
        c.addEventListener('touchend',   function(e) { e.preventDefault(); drawing = false; }, { passive: false });
    }

    function init() {
        var c = getCanvas();
        if (!c) { setTimeout(init, 200); return; }
        ctx = c.getContext('2d');
        bindCanvas(c);
    }

    document.addEventListener('DOMContentLoaded', init);

    window.editSigTab = function(tab) {
        var isDraw = tab === 'draw';
        document.getElementById('edit-panel-draw').style.display   = isDraw ? 'block' : 'none';
        document.getElementById('edit-panel-upload').style.display = isDraw ? 'none'  : 'block';
        document.getElementById('edit-tab-draw').style.background   = isDraw ? '#528CBE' : 'white';
        document.getElementById('edit-tab-draw').style.color        = isDraw ? 'white'   : '#6b7280';
        document.getElementById('edit-tab-upload').style.background = isDraw ? 'white'   : '#528CBE';
        document.getElementById('edit-tab-upload').style.color      = isDraw ? '#6b7280' : 'white';
        document.getElementById('edit-sig-data').value = '';
        msg(false);
    };

    window.clearEditSig = function() {
        var c = getCanvas(); if (!c) return;
        c.getContext('2d').clearRect(0, 0, c.width, c.height);
        document.getElementById('edit-sig-data').value = '';
        c.style.borderColor = '#d1d5db';
        msg(false);
    };

    window.captureEditSig = function() {
        var c = getCanvas(); if (!c) return;
        document.getElementById('edit-sig-data').value = c.toDataURL('image/png');
        c.style.borderColor = '#10b981';
        msg(true);
    };

    window.editSigFileSelected = function(input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit-sig-data').value = e.target.result;
            var preview = document.getElementById('edit-sig-file-preview');
            var img     = document.getElementById('edit-sig-file-img');
            img.src     = e.target.result;
            preview.style.display = 'block';
            msg(true);
        };
        reader.readAsDataURL(file);
    };

    window.clearEditSig = function() {
        var c = getCanvas(); if (c) { c.getContext('2d').clearRect(0, 0, c.width, c.height); c.style.borderColor = '#d1d5db'; }
        document.getElementById('edit-sig-data').value = '';
        var preview = document.getElementById('edit-sig-file-preview');
        if (preview) preview.style.display = 'none';
        msg(false);
    };
})();
</script>

@endsection

