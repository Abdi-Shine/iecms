@extends('admin.admin_master')
@section('page_title', 'Finance Dashboard')
@section('admin_main_content')


<div class="p-4 sm:p-8 bg-[#f8fafc] min-h-screen">
    <div class="max-w-[1400px] mx-auto">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-neutral-800 tracking-tight">Finance Dashboard</h1>
                <p class="text-sm text-neutral-500 font-medium mt-1">
                    <i class="bi bi-geo-alt-fill text-[#528CBE]"></i> Western District Court — Financial Overview
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('finance.payments') }}" class="px-5 py-2.5 bg-[#528CBE] text-white rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-[#3D78AB] transition-all shadow-lg shadow-blue-500/20">
                    <i class="bi bi-eye"></i> View All Payments
                </a>
            </div>
        </div>

        {{-- KPI Cards Row 1 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Payments --}}
            <div class="kpi-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-1">Total Payments</p>
                        <h3 class="text-3xl font-black text-neutral-800">{{ number_format($stats['total_payments']) }}</h3>
                    </div>
                    <div class="kpi-icon bg-blue-50 text-[#528CBE]">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs font-bold text-emerald-500 flex items-center">
                        <i class="bi bi-arrow-up-short"></i> +12%
                    </span>
                    <span class="text-[10px] text-neutral-400 font-medium">vs last month</span>
                </div>
            </div>

            {{-- Approved Payments --}}
            <div class="kpi-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-1">Approved</p>
                        <h3 class="text-3xl font-black text-neutral-800">{{ number_format($stats['approved_payments']) }}</h3>
                    </div>
                    <div class="kpi-icon bg-emerald-50 text-emerald-500">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div class="mt-4 w-full bg-neutral-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full" style="width: {{ $stats['total_payments'] > 0 ? ($stats['approved_payments'] / $stats['total_payments'] * 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Total Revenue --}}
            <div class="kpi-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-1">Total Revenue</p>
                        <h3 class="text-2xl font-black text-neutral-800">${{ number_format($stats['total_revenue'], 2) }} <span class="text-xs text-neutral-400 uppercase">USD</span></h3>
                    </div>
                    <div class="kpi-icon bg-purple-50 text-purple-500">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
                <p class="mt-4 text-[10px] text-neutral-400 font-bold uppercase tracking-wider">All time collected</p>
            </div>

            {{-- Monthly Revenue --}}
            <div class="kpi-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-1">This Month</p>
                        <h3 class="text-2xl font-black text-neutral-800">${{ number_format($stats['monthly_revenue'], 2) }} <span class="text-xs text-neutral-400 uppercase">USD</span></h3>
                    </div>
                    <div class="kpi-icon bg-orange-50 text-orange-500">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-[10px] text-neutral-500 font-bold uppercase">{{ now()->format('F Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Status Breakdown Pills --}}
        <div class="card mb-8">
            <h4 class="text-[11px] font-black text-neutral-400 uppercase tracking-widest mb-6 text-center">Payment Status Breakdown</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                {{-- Pending --}}
                <div class="status-pill text-amber-500 bg-amber-50/30">
                    <span class="text-2xl font-black">{{ $status_breakdown['Pending'] ?? 0 }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Pending</span>
                </div>
                {{-- Awaiting Approval --}}
                <div class="status-pill text-purple-500 bg-purple-50/30">
                    <span class="text-2xl font-black">{{ $status_breakdown['Awaiting Approval'] ?? 0 }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-center leading-tight">Awaiting Approval</span>
                </div>
                {{-- Processing --}}
                <div class="status-pill text-blue-500 bg-blue-50/30">
                    <span class="text-2xl font-black">{{ $status_breakdown['Processing'] ?? 0 }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Processing</span>
                </div>
                {{-- Completed --}}
                <div class="status-pill text-emerald-500 bg-emerald-50/30">
                    <span class="text-2xl font-black">{{ $status_breakdown['Approved'] ?? 0 }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Completed</span>
                </div>
                {{-- Failed --}}
                <div class="status-pill text-red-500 bg-red-50/30">
                    <span class="text-2xl font-black">{{ $status_breakdown['Failed'] ?? 0 }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Failed</span>
                </div>
            </div>
        </div>

        {{-- Row 3: Smaller KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="kpi-card flex items-center justify-between border-l-4 border-l-[#528CBE]">
                <div>
                    <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Today's Revenue</p>
                    <h4 class="text-xl font-black text-neutral-800">${{ number_format($stats['today_revenue'], 2) }}</h4>
                </div>
                <i class="bi bi-clock-history text-2xl text-neutral-100"></i>
            </div>
            <div class="kpi-card flex items-center justify-between border-l-4 border-l-emerald-500">
                <div>
                    <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">This Year Revenue</p>
                    <h4 class="text-xl font-black text-neutral-800">${{ number_format($stats['yearly_revenue'], 2) }}</h4>
                </div>
                <i class="bi bi-graph-up-arrow text-2xl text-neutral-100"></i>
            </div>
            <div class="kpi-card flex items-center justify-between border-l-4 border-l-purple-500">
                <div>
                    <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest">Total Receipts</p>
                    <h4 class="text-xl font-black text-neutral-800">{{ $stats['approved_payments'] }}</h4>
                </div>
                <i class="bi bi-receipt text-2xl text-neutral-100"></i>
            </div>
        </div>

        {{-- Recent Payments Table --}}
        <div class="table-container">
            <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/30">
                <h3 class="text-sm font-black text-neutral-800 uppercase tracking-wider">Recent Payments</h3>
                <a href="{{ route('finance.payments') }}" class="text-[10px] font-black text-[#528CBE] uppercase tracking-widest hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-neutral-50/20">
                            <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest">Payment ID</th>
                            <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest">Payer</th>
                            <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest">Amount</th>
                            <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest">Date</th>
                            <th class="px-6 py-4 text-[10px] font-black text-neutral-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse($recent_payments as $payment)
                            <tr class="hover:bg-neutral-50/40 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-xs font-black text-[#528CBE]">#PAY-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-neutral-800">{{ $payment->payer_name }}</div>
                                    <div class="text-[10px] text-neutral-400 font-medium">TxID: {{ $payment->transaction_id ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-black text-neutral-700">${{ number_format($payment->amount, 2) }}</span>
                                    <span class="text-[9px] text-neutral-400 font-bold uppercase ml-1">{{ $payment->currency }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStyles = [
                                            'Approved' => 'bg-emerald-50 text-emerald-600',
                                            'Pending' => 'bg-amber-50 text-amber-600',
                                            'Failed' => 'bg-red-50 text-red-600',
                                            'Processing' => 'bg-blue-50 text-blue-600',
                                            'Awaiting Approval' => 'bg-purple-50 text-purple-600',
                                        ];
                                        $style = $statusStyles[$payment->status] ?? 'bg-neutral-50 text-neutral-500';
                                    @endphp
                                    <span class="status-badge {{ $style }}">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-bold text-neutral-500">{{ $payment->payment_date ? date('d/m/Y', strtotime($payment->payment_date)) : 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="w-8 h-8 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-400 hover:text-[#528CBE] transition-all">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($payment->status === 'Pending')
                                            <button class="w-8 h-8 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-400 hover:text-emerald-500 transition-all">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-neutral-400 italic text-sm">
                                    No recent payments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
