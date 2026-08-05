@extends('admin.admin_master')
@section('page_title', 'Interviews — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Interviews</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }}</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Case
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Record Interview</h3>
            <form action="{{ route('criminal-cases.interviews.store', $case->id) }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Interviewee Name</label>
                        <input type="text" name="interviewee_name" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">ID Number</label>
                        <input type="text" name="interviewee_id" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Role</label>
                        <select name="interviewee_role" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            @foreach(\App\Models\CriminalCaseInterview::ROLES as $r)
                                <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Date</label>
                        <input type="date" name="interview_date" required value="{{ date('Y-m-d') }}" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Time</label>
                        <input type="time" name="interview_time" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Location</label>
                        <input type="text" name="interview_location" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Interviewing Officer</label>
                        <input type="text" name="interviewing_officer" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Second Officer / Witness</label>
                        <input type="text" name="second_officer_witness" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Format</label>
                        <select name="format" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            @foreach(\App\Models\CriminalCaseInterview::FORMATS as $f)
                                <option value="{{ $f }}">{{ ucfirst($f) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:1rem">
                    <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Statement Summary</label>
                    <textarea name="statement_summary" rows="3" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem"></textarea>
                </div>
                <button type="submit" style="padding:.65rem 1.25rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                    <i class="bi bi-mic-fill"></i> Record Interview
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($case->interviews as $iv)
            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h4 class="text-sm font-bold text-neutral-800">{{ $iv->interviewee_name }} &mdash; {{ ucfirst($iv->interviewee_role) }}</h4>
                        <p class="text-xs text-neutral-500">{{ $iv->interview_date->format('Y-m-d') }} {{ $iv->interview_time }} &middot; {{ ucfirst($iv->format) }} &middot; by {{ $iv->interviewing_officer }}</p>
                    </div>
                    @if($iv->signed_off_at)
                        <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-green-50 text-green-700">Signed Off</span>
                    @else
                        <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-amber-50 text-amber-700">Pending Sign-off</span>
                    @endif
                </div>
                @if($iv->statement_summary)
                    <p class="text-sm text-neutral-600 mb-3">{{ $iv->statement_summary }}</p>
                @endif
                @if(!$iv->signed_off_at)
                    <form action="{{ route('criminal-cases.interviews.sign-off', [$case->id, $iv->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#16A34A">Sign Off Statement</button>
                    </form>
                @else
                    <p class="text-xs text-neutral-400">Signed off by {{ $iv->signedOffBy->name ?? '—' }} on {{ $iv->signed_off_at->format('Y-m-d H:i') }}</p>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">No interviews recorded yet.</div>
        @endforelse
    </div>

</div>

@endsection
