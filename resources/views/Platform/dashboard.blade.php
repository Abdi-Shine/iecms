@extends('admin.admin_master')
@section('page_title', 'Global Dashboard')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="mb-6 mt-2">
        <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Global Dashboard</h1>
        <p class="text-sm text-neutral-500 mt-0.5">Platform-wide overview across every institution</p>
    </div>

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.12)">
                <i class="bi bi-diagram-3 text-xl" style="color:#528CBE"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Institutions</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['total_institutions'] }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.12)">
                <i class="bi bi-check-circle text-xl" style="color:#10b981"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Active</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['active_institutions'] }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(240,180,60,0.12)">
                <i class="bi bi-people text-xl" style="color:#F0B43C"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Total Users</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['total_users'] }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-neutral-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(124,58,237,0.12)">
                <i class="bi bi-shield-lock text-xl" style="color:#7C3AED"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-neutral-400 uppercase tracking-widest">Super Admins</p>
                <h3 class="text-3xl font-black text-neutral-800">{{ $stats['super_admins'] }}</h3>
            </div>
        </div>
    </div>

    {{-- Per-institution breakdown --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-bar-chart text-sm" style="color:#528CBE"></i>
                <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Per-Institution Breakdown</span>
            </div>
            <a href="{{ route('institutions.index') }}" class="text-xs font-semibold" style="color:#528CBE">Manage Institutions →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Institution</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Type</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Users</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Cases/Records</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($institutions as $institution)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-4 py-4 font-semibold text-neutral-800">{{ $institution->name }}</td>
                        <td class="px-4 py-4 text-neutral-500">{{ ucfirst($institution->type) }}</td>
                        <td class="px-4 py-4 text-center">{{ $institution->users_count }}</td>
                        <td class="px-4 py-4 text-center">{{ $institution->case_count }}</td>
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
