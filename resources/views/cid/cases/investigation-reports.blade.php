@extends('admin.admin_master')
@section('page_title', 'Investigation Reports — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Investigation Reports</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; Progress / Interim / Expert-Forensic reports</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Case
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">New Report</h3>
            <form action="{{ route('criminal-cases.investigation-reports.store', $case->id) }}" method="POST">
                @csrf
                <div style="margin-bottom:1rem">
                    <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Report Type</label>
                    <select name="report_type" required style="width:100%;max-width:320px;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                        @foreach(\App\Models\CriminalCaseInvestigationReport::TYPES as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:1rem">
                    <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Content</label>
                    <textarea name="content" rows="4" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem"></textarea>
                </div>
                <button type="submit" style="padding:.65rem 1.25rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                    <i class="bi bi-file-earmark-plus"></i> Save Draft
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($case->investigationReports as $report)
            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h4 class="text-sm font-bold text-neutral-800">{{ $report->report_type }}</h4>
                        <p class="text-xs text-neutral-500">By {{ $report->author->name ?? '—' }} &middot; {{ $report->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @php
                        $sColor = match($report->status) {
                            'Approved','Submitted' => ['#DCFCE7','#16A34A'],
                            'Supervisor Review' => ['#FEF3C7','#B45309'],
                            default => ['#F3F4F6','#6B7280']
                        };
                    @endphp
                    <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full" style="background:{{ $sColor[0] }};color:{{ $sColor[1] }}">{{ $report->status }}</span>
                </div>
                <p class="text-sm text-neutral-600 mb-3">{{ $report->content }}</p>

                <div class="flex gap-2">
                    @if($report->status === 'Draft')
                        <form action="{{ route('criminal-cases.investigation-reports.submit-for-review', [$case->id, $report->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#528CBE">Submit for Review</button>
                        </form>
                    @elseif($report->status === 'Supervisor Review')
                        <form action="{{ route('criminal-cases.investigation-reports.approve', [$case->id, $report->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#16A34A">Approve (Supervisor)</button>
                        </form>
                    @elseif($report->status === 'Approved')
                        <form action="{{ route('criminal-cases.investigation-reports.submit', [$case->id, $report->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#7C3AED">Mark Submitted</button>
                        </form>
                    @else
                        <p class="text-xs text-neutral-400">Reviewed by {{ $report->reviewer->name ?? '—' }} on {{ optional($report->reviewed_at)->format('Y-m-d') }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">No investigation reports yet.</div>
        @endforelse
    </div>

</div>

@endsection
