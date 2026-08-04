@extends('admin.admin_master')
@section('page_title', 'Access Management')
@section('admin_main_content')


<div class="p-4 sm:p-6 w-full bg-[#f8fafc] min-h-screen">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Access Management</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Control system access and manage security credentials</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        {{-- Total Workforce --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-people-fill text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total Staff</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">{{ $stats['total'] }}</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Registered workforce</p>
            </div>
        </div>

        {{-- Active Credentials --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-shield-check text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Active Access</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                    {{ $stats['active'] }}
                </h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">System credentials</p>
            </div>
        </div>

        {{-- Access Required --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-person-plus-fill text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Needs Access</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">
                    {{ $stats['total'] - $stats['active'] }}
                </h3>
                <p class="text-xs font-medium mt-0.5" style="color:#528CBE">Pending configuration</p>
            </div>
        </div>

        {{-- Live Sessions --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-clock-history text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Live Sessions</p>
                <h3 class="text-3xl font-black text-neutral-800 leading-tight">0</h3>
                <p class="text-xs font-medium mt-0.5" style="color:#F0B43C">Currently active</p>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
        
        {{-- Search & Filter --}}
        <div class="px-6 pt-5 pb-4 border-b border-neutral-100">
            <form action="{{ route('employee.access-login') }}" method="GET"
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
                <select name="court" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           focus:outline-none focus:border-[#528CBE] min-w-[180px] cursor-pointer">
                    <option value="">All Courts</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->courtcode }}" {{ request('court') == $court->courtcode ? 'selected' : '' }}>
                            {{ $court->longName }}
                        </option>
                    @endforeach
                </select>

                {{-- Status filter --}}
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-neutral-50 text-neutral-600
                           focus:outline-none focus:border-[#528CBE] min-w-[140px] cursor-pointer">
                    <option value="">All Access Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Has Access</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>No Access</option>
                </select>

                {{-- Search button --}}
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition hover:opacity-90 flex items-center gap-2"
                    style="background:#528CBE">
                    <i class="bi bi-search"></i> Search
                </button>

                @if(request()->anyFilled(['search','court','status']))
                <a href="{{ route('employee.access-login') }}"
                   class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-500 hover:bg-neutral-50 transition flex items-center gap-2">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-neutral-50/50">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-neutral-500 border-b border-neutral-100">S/N</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-neutral-500 border-b border-neutral-100">Staff Member</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-neutral-500 border-b border-neutral-100">Unit/Court</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-neutral-500 border-b border-neutral-100">Username</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-neutral-500 border-b border-neutral-100">Role</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-neutral-500 border-b border-neutral-100">Permission Group</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-neutral-500 border-b border-neutral-100">Security</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-neutral-500 border-b border-neutral-100 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($employees as $index => $employee)
                    <tr class="hover:bg-neutral-50/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-neutral-400">{{ $employees->firstItem() + $index }}</td>
                        <td class="px-6 py-4">
                            <div>
                                <span class="font-bold text-neutral-800 text-sm block">{{ $employee->EmpName }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-neutral-600">{{ $employee->court->longName ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-neutral-500 italic">{{ $employee->system_username ?? 'unassigned' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg bg-neutral-100 text-neutral-600 text-[10px] font-bold uppercase tracking-wider">
                                {{ $employee->Position ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($employee->user?->group)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-white text-[10px] font-bold"
                                      style="background:{{ $employee->user->group->color }}">
                                    <i class="bi bi-collection text-[9px]"></i>
                                    {{ $employee->user->group->name }}
                                </span>
                            @elseif($employee->islogin == '1')
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold" style="background:#fef3c7;color:#92400e">
                                    Super Admin
                                </span>
                            @else
                                <span class="text-neutral-300 text-[10px]">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($employee->islogin == '1')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Authorized
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-neutral-100 text-neutral-500 text-[10px] font-bold">
                                    <div class="w-1.5 h-1.5 rounded-full bg-neutral-400"></div> No Access
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                @if($employee->islogin == '1')
                                    <a href="{{ route('employee.access-login.edit', $employee->AID) }}"
                                       class="flex items-center gap-2 px-4 py-2 text-white text-[11px] font-bold uppercase tracking-widest rounded-xl transition-all active:scale-95 shadow-sm"
                                       style="background:#F0B43C">
                                        <i class="bi bi-pencil-fill"></i> Edit Access
                                    </a>
                                @else
                                    <a href="{{ route('employee.access-login.create', $employee->AID) }}"
                                       class="flex items-center gap-2 px-4 py-2 text-white text-[11px] font-bold uppercase tracking-widest rounded-xl transition-all active:scale-95 shadow-sm"
                                       style="background:#528CBE">
                                        <i class="bi bi-shield-lock-fill"></i> Grant Access
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-neutral-400 italic">
                                <i class="bi bi-person-x text-3xl"></i>
                                <p class="text-sm">No staff records found for access configuration</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 bg-neutral-50/50 border-t border-neutral-100">
            {{ $employees->links() }}
        </div>
    </div>
</div>

@endsection
