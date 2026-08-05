@extends('admin.admin_master')
@section('page_title', 'Platform Users')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="mb-6 mt-2">
        <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Platform Users</h1>
        <p class="text-sm text-neutral-500 mt-0.5">Every user across every institution</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">

        <form action="{{ route('platform.users.index') }}" method="GET" class="reg-filter">
            <select name="institution_id" onchange="this.form.submit()" class="reg-filter-sel">
                <option value="">All Institutions</option>
                @foreach($institutions as $institution)
                    <option value="{{ $institution->id }}" {{ request('institution_id') == $institution->id ? 'selected' : '' }}>{{ $institution->name }}</option>
                @endforeach
            </select>
            <select name="tier" onchange="this.form.submit()" class="reg-filter-sel">
                <option value="">All Tiers</option>
                @foreach(['Super Admin', 'Institution Admin', 'Staff'] as $tier)
                    <option value="{{ $tier }}" {{ request('tier') == $tier ? 'selected' : '' }}>{{ $tier }}</option>
                @endforeach
            </select>
            @if(request()->anyFilled(['institution_id','tier']))
                <a href="{{ route('platform.users.index') }}" class="btn-outline btn-sm">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            @endif
        </form>

        <div class="px-6 py-4 flex items-center justify-between">
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">User Directory</span>
            <span class="text-xs text-neutral-400 font-medium">{{ $users->count() }} {{ Str::plural('user', $users->count()) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr style="background:rgba(82,140,190,0.06)">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Name</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Email</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Institution</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Tier</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500 text-center">2FA</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-neutral-500">Last Login</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-4 py-4 font-semibold text-neutral-800">{{ $user->name }}</td>
                        <td class="px-4 py-4 text-neutral-500">{{ $user->email }}</td>
                        <td class="px-4 py-4 text-neutral-500">{{ $user->institution?->name ?? '—' }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold"
                                  style="background:rgba(82,140,190,0.1);color:#528CBE">
                                {{ $user->tier }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($user->hasTwoFactorEnabled())
                                <i class="bi bi-shield-check" style="color:#059669" title="2FA enabled"></i>
                            @elseif($user->requiresTwoFactor())
                                <i class="bi bi-shield-exclamation" style="color:#DC2626" title="2FA required but not enabled"></i>
                            @else
                                <span class="text-neutral-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-neutral-500 text-xs">{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('Y-m-d H:i') : 'Never' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-neutral-400 font-medium text-sm">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
