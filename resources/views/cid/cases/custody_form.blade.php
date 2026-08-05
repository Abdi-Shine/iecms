@extends('admin.admin_master')
@section('page_title', 'Stage 4 — Custody & Court Scheduling')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Stage 4: Custody &amp; Court Scheduling</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }}</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back
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

    @php $c = $case->custody; @endphp

    {{-- Custody Management --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">A. Custody Management</h3>
            <form action="{{ route('criminal-cases.workflow.custody.store', $case->id) }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Custody Status <span style="color:#ef4444">*</span></label>
                        <select name="custody_status" required style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            @foreach(\App\Models\CriminalCaseCustody::STATUSES as $status)
                                <option value="{{ $status }}" {{ old('custody_status', $c->custody_status ?? 'In Custody') == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Custody Location</label>
                        <input type="text" name="custody_location" value="{{ old('custody_location', $c->custody_location ?? '') }}" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Cell / Unit Reference</label>
                        <input type="text" name="cell_unit_reference" value="{{ old('cell_unit_reference', $c->cell_unit_reference ?? '') }}" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Custody Start Date <span style="color:#ef4444">*</span></label>
                        <input type="date" name="custody_start_date" required value="{{ old('custody_start_date', optional($c?->custody_start_date)->format('Y-m-d') ?? date('Y-m-d')) }}" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Legal Deadline (Remand Expiry)</label>
                        <input type="date" name="legal_deadline" value="{{ old('legal_deadline', optional($c?->legal_deadline)->format('Y-m-d')) }}" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Custody Review Date</label>
                        <input type="date" name="custody_review_date" value="{{ old('custody_review_date', optional($c?->custody_review_date)->format('Y-m-d')) }}" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Bail Conditions</label>
                        <textarea name="bail_conditions" rows="2" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">{{ old('bail_conditions', $c->bail_conditions ?? '') }}</textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Welfare Notes</label>
                        <textarea name="welfare_notes" rows="2" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">{{ old('welfare_notes', $c->welfare_notes ?? '') }}</textarea>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Custody Review Officer</label>
                        <input type="text" name="custody_review_officer" value="{{ old('custody_review_officer', $c->custody_review_officer ?? '') }}" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Medical Check Status</label>
                        <input type="text" name="medical_check_status" value="{{ old('medical_check_status', $c->medical_check_status ?? '') }}" style="width:100%;padding:.65rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end">
                    <button type="submit" style="padding:.75rem 2rem;font-size:.8rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer;text-transform:uppercase">
                        <i class="bi bi-check-circle-fill"></i> Save Custody Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Court Scheduling --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">B. Court Scheduling</h3>

            <form action="{{ route('criminal-cases.workflow.court-appearances.store', $case->id) }}" method="POST" style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #f3f4f6">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Date <span style="color:#ef4444">*</span></label>
                        <input type="date" name="appearance_date" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Time</label>
                        <input type="time" name="appearance_time" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Court Name <span style="color:#ef4444">*</span></label>
                        <input type="text" name="court_name" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Hearing Type <span style="color:#ef4444">*</span></label>
                        <select name="hearing_type" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            @foreach(\App\Models\CriminalCaseCourtAppearance::HEARING_TYPES as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;align-items:end">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Room Number</label>
                        <input type="text" name="room_number" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Responsible Officer</label>
                        <input type="text" name="responsible_officer" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <button type="submit" style="padding:.65rem 1rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                        <i class="bi bi-calendar-plus"></i> Schedule Appearance
                    </button>
                </div>
            </form>

            <div class="space-y-3">
                @forelse($case->courtAppearances as $appearance)
                    <div class="border border-neutral-100 rounded-xl p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-neutral-800">{{ $appearance->hearing_type }} &mdash; {{ $appearance->court_name }}</h4>
                                <p class="text-xs text-neutral-500">
                                    {{ $appearance->appearance_date->format('Y-m-d') }}
                                    @if($appearance->appearance_time) at {{ $appearance->appearance_time }} @endif
                                    @if($appearance->room_number) &middot; Room {{ $appearance->room_number }} @endif
                                    @if($appearance->responsible_officer) &middot; {{ $appearance->responsible_officer }} @endif
                                </p>
                            </div>
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">{{ $appearance->file_readiness_status }}</span>
                        </div>

                        @if($appearance->outcome)
                            <p class="text-xs text-neutral-600 mt-2"><strong>Outcome:</strong> {{ $appearance->outcome }}
                                @if($appearance->next_hearing_date) &mdash; Next: {{ $appearance->next_hearing_date->format('Y-m-d') }} @endif
                            </p>
                        @else
                            <form action="{{ route('criminal-cases.workflow.court-appearances.outcome', [$case->id, $appearance->id]) }}" method="POST" class="mt-3 flex gap-2">
                                @csrf
                                <input type="text" name="outcome" placeholder="Record hearing outcome" required class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg flex-1">
                                <input type="date" name="next_hearing_date" class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg">
                                <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#16A34A">Record</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-neutral-400 text-center py-6">No court appearances scheduled yet.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
