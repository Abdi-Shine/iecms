@extends('admin.admin_master')
@section('page_title', 'Remand Management')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Remand Management</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Legal compliance dashboard &mdash; live view, computed on each page load</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-neutral-100 p-6">
            <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wide mb-2">Compliance Rate</p>
            <h3 class="text-3xl font-black" style="color:{{ $compliancePercent >= 90 ? '#16A34A' : ($compliancePercent >= 70 ? '#B45309' : '#DC2626') }}">{{ $compliancePercent }}%</h3>
        </div>
        <div class="bg-white rounded-2xl border border-neutral-100 p-6">
            <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wide mb-2">Within Legal Limits</p>
            <h3 class="text-3xl font-black text-neutral-800">{{ $withinLimit }} / {{ $total }}</h3>
        </div>
        <div class="bg-white rounded-2xl border border-neutral-100 p-6">
            <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wide mb-2">Escalations (72h)</p>
            <h3 class="text-3xl font-black text-red-600">{{ $escalations->count() }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <div style="padding:1.5rem 2rem 1rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151">Escalation List</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Detainee</th>
                    <th class="px-5 py-3">Case</th>
                    <th class="px-5 py-3">Legal Deadline</th>
                    <th class="px-5 py-3">Alert Level</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($escalations as $d)
                    @php
                        $hours = now()->diffInHours($d->legal_deadline, false);
                        $level = $hours < 0 ? 'Overdue' : ($hours <= 24 ? 'Critical (24h)' : ($hours <= 48 ? 'Warning (48h)' : 'Notice (72h)'));
                        $color = $hours < 0 || $hours <= 24 ? ['#FEE2E2','#DC2626'] : ($hours <= 48 ? ['#FEF3C7','#B45309'] : ['#F3F4F6','#6B7280']);
                    @endphp
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $d->detainee_name }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $d->criminalCase->case_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $d->legal_deadline->format('Y-m-d') }}</td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full" style="background:{{ $color[0] }};color:{{ $color[1] }}">{{ $level }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('cid-detainees.show', $d->id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-neutral-400">No detainees approaching remand expiry within 72 hours.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
