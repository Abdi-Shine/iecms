@extends('admin.admin_master')
@section('page_title', 'Stage 5 — Final Report & AGO Submission')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Stage 5: Final Report &amp; AGO Submission</h1>
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

    @php $r = $case->finalReport; @endphp

    @if($r?->isSubmitted())
        <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-700">
            <i class="bi bi-check-circle-fill"></i>
            Submitted to AGO on {{ $r->submitted_to_ago_at->format('Y-m-d H:i') }}
            @if($r->agoReceivingOfficer) to {{ $r->agoReceivingOfficer->name }} @endif.
            AGO case reference: <strong>{{ $r->attorneyCase->case_number ?? '—' }}</strong>.
            This report is now locked.
        </div>
    @elseif($r?->supervisor_endorsed_at)
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">
            <i class="bi bi-patch-check-fill"></i>
            Endorsed by {{ $r->supervisor->name ?? 'supervisor' }} on {{ $r->supervisor_endorsed_at->format('Y-m-d H:i') }}. Ready for AGO submission.
        </div>
    @endif

    @if($r)
        <div class="mb-4 p-4 rounded-xl bg-neutral-50 border border-neutral-100 text-sm text-neutral-600">
            Report Number: <strong>{{ $r->report_number }}</strong>
        </div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:2rem 2.25rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">A. Final Investigation Report</h3>
            <fieldset {{ $r?->isSubmitted() ? 'disabled' : '' }}>
                <form action="{{ route('criminal-cases.workflow.report.store', $case->id) }}" method="POST">
                    @csrf
                    <div style="display:flex;flex-direction:column;gap:1.5rem">

                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Case Summary <span style="color:#ef4444">*</span></label>
                            <textarea name="case_summary" rows="4" required
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('case_summary', $r->case_summary ?? '') }}</textarea>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                            <div>
                                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Suspect Profile Summary</label>
                                <textarea name="suspect_profile_summary" rows="3"
                                    style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('suspect_profile_summary', $r->suspect_profile_summary ?? '') }}</textarea>
                            </div>
                            <div>
                                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Witness List &amp; Statement Summary</label>
                                <textarea name="witness_summary" rows="3"
                                    style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('witness_summary', $r->witness_summary ?? '') }}</textarea>
                            </div>
                        </div>

                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Applicable Law / Charges (statute references)</label>
                            <textarea name="applicable_law" rows="2"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('applicable_law', $r->applicable_law ?? '') }}</textarea>
                        </div>

                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Investigator's Recommendation <span style="color:#ef4444">*</span></label>
                            <select name="recommendation" required style="width:100%;max-width:400px;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                                <option value="">Select recommendation</option>
                                @foreach(\App\Models\CriminalCaseFinalReport::RECOMMENDATIONS as $rec)
                                    <option value="{{ $rec }}" {{ old('recommendation', $r->recommendation ?? '') == $rec ? 'selected' : '' }}>{{ $rec }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end">
                        <button type="submit"
                            style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em">
                            <i class="bi bi-save-fill"></i> Save Report
                        </button>
                    </div>
                </form>
            </fieldset>

            @if($r && !$r->supervisor_endorsed_at)
                <form action="{{ route('criminal-cases.workflow.report.endorse', $case->id) }}" method="POST"
                      style="margin-top:1rem;padding-top:1.5rem;border-top:1px solid #f3f4f6">
                    @csrf
                    <p style="font-size:.8rem;color:#6b7280;margin-bottom:1rem">
                        A supervisor (Investigator or Institution Admin) must endorse this report before AGO submission.
                    </p>
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#16A34A;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em">
                        <i class="bi bi-patch-check-fill"></i> Supervisor Endorse
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- AGO Submission --}}
    @if($r?->supervisor_endorsed_at && !$r->isSubmitted())
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
            <div style="padding:2rem 2.25rem">
                <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">B. AGO Submission</h3>
                <form action="{{ route('criminal-cases.workflow.report.submit-to-ago', $case->id) }}" method="POST">
                    @csrf
                    <div style="margin-bottom:1.5rem;max-width:400px">
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Receiving AGO Officer</label>
                        <select name="ago_receiving_officer_id" style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                            <option value="">Select AGO officer</option>
                            @foreach($agoOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#7C3AED;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em">
                        <i class="bi bi-send-fill"></i> Submit to AGO
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>

@endsection
