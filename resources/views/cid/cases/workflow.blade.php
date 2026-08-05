@extends('admin.admin_master')
@section('page_title', 'Case Workflow — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $case->case_number }}</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Investigation Workflow &mdash; {{ $case->priority }} priority</p>
        </div>
        <div class="flex gap-2 flex-wrap justify-end" x-data="{ moreOpen: false }">
            <a href="{{ route('criminal-cases.diary', $case->id) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-journal-text"></i> Diary
            </a>
            <div class="relative">
                <button @click="moreOpen = !moreOpen" @click.outside="moreOpen = false"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                    <i class="bi bi-three-dots"></i> More
                </button>
                <div x-show="moreOpen" x-transition style="display:none" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-neutral-100 py-2 z-20">
                    <a href="{{ route('criminal-cases.takeovers', $case->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-arrow-left-right"></i> Takeovers</a>
                    <a href="{{ route('criminal-cases.biometrics.index', $case->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-fingerprint"></i> Biometrics</a>
                    <a href="{{ route('criminal-cases.interviews.index', $case->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-mic-fill"></i> Interviews</a>
                    <a href="{{ route('criminal-cases.investigation-reports.index', $case->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-file-earmark-text"></i> Investigation Reports</a>
                    <a href="{{ route('criminal-cases.court-forms.index', $case->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-file-earmark-check"></i> Court Forms</a>
                    <div class="border-t border-neutral-100 my-1"></div>
                    @if($case->detainee)
                        <a href="{{ route('cid-detainees.show', $case->detainee->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-shield-lock"></i> Detention Record</a>
                    @else
                        <a href="{{ route('cid-detainees.admission-form', $case->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-shield-lock"></i> New Detention Admission</a>
                    @endif
                    <a href="{{ route('criminal-cases.exhibits.index', $case->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-box-seam"></i> Exhibits</a>
                    <div class="border-t border-neutral-100 my-1"></div>
                    <a href="{{ route('cid-legal-process.form', [$case->id, 'arrest-without-warrant-ago']) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-send"></i> Arrest Without Warrant (AGO)</a>
                    <a href="{{ route('cid-legal-process.form', [$case->id, 'warrant-of-arrest-ago']) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-send"></i> Warrant of Arrest (AGO)</a>
                    <a href="{{ route('cid-legal-process.form', [$case->id, 'search-seizure-ago']) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-send"></i> Search &amp; Seizure (AGO)</a>
                    <a href="{{ route('cid-legal-process.form', [$case->id, 'asset-recovery-ago']) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-send"></i> Asset Recovery (AGO)</a>
                    <a href="{{ route('cid-legal-process.form', [$case->id, 'search-warrants']) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50"><i class="bi bi-file-earmark-text"></i> Search Warrant (Court)</a>
                </div>
            </div>
            <a href="{{ route('criminal-cases.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-arrow-left"></i> Back to Cases
            </a>
        </div>
    </div>

    <div class="space-y-3">
        @foreach($steps as $index => $step)
            <div class="bg-white rounded-2xl border border-neutral-100 p-5 flex items-center justify-between {{ $step['enabled'] ? '' : 'opacity-50' }}">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                         style="background:{{ $step['complete'] ? '#DCFCE7' : '#F3F4F6' }};color:{{ $step['complete'] ? '#16A34A' : '#6B7280' }}">
                        @if($step['complete'])
                            <i class="bi bi-check-lg"></i>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-neutral-800">{{ $step['title'] }}</h3>
                        <p class="text-xs text-neutral-500">{{ $step['description'] }}</p>
                    </div>
                </div>
                @if($step['route'])
                    <a href="{{ $step['route'] }}"
                       class="px-4 py-2 text-[13px] font-semibold rounded-lg text-white transition"
                       style="background:#528CBE">
                        {{ $step['complete'] ? 'Review' : 'Open' }}
                    </a>
                @else
                    <span class="text-[11px] font-bold uppercase tracking-wide text-neutral-400">
                        {{ $step['enabled'] ? 'Coming soon' : 'Locked' }}
                    </span>
                @endif
            </div>
        @endforeach
    </div>

</div>

@endsection
