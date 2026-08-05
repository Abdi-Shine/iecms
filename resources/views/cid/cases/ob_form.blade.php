@extends('admin.admin_master')
@section('page_title', 'Stage 2 — Occurrence Book')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Stage 2: Occurrence Book</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; Initial Report &amp; Assignment</p>
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

    @php $ob = $case->occurrenceBook; @endphp

    @if($ob?->supervisor_acknowledged_at)
        <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-700 flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            Acknowledged by {{ $ob->supervisor->name ?? 'supervisor' }} on {{ $ob->supervisor_acknowledged_at->format('Y-m-d H:i') }}. Stage 3 unlocked.
        </div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <div style="padding:2rem 2.25rem">

            @if($ob)
                <div style="margin-bottom:1.5rem">
                    <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">OB Reference Number</label>
                    <input type="text" value="{{ $ob->ob_number }}" readonly tabindex="-1"
                        style="width:100%;max-width:320px;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#f3f4f6;color:#6b7280;cursor:not-allowed">
                </div>
            @endif

            <form action="{{ route('criminal-cases.workflow.ob.store', $case->id) }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:1.5rem">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">OB Entry Date/Time <span style="color:#ef4444">*</span></label>
                            <input type="datetime-local" name="ob_datetime" required
                                value="{{ old('ob_datetime', $ob?->ob_datetime?->format('Y-m-d\TH:i')) }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Reporting Officer <span style="color:#ef4444">*</span></label>
                            <input type="text" name="reporting_officer" required value="{{ old('reporting_officer', $ob->reporting_officer ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Nature of Offence <span style="color:#ef4444">*</span></label>
                            <input type="text" name="offence_nature" required value="{{ old('offence_nature', $ob->offence_nature ?? $case->arrest?->alleged_offence) }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Incident Date/Time</label>
                            <input type="datetime-local" name="incident_datetime" value="{{ old('incident_datetime', $ob?->incident_datetime?->format('Y-m-d\TH:i')) }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Incident Location</label>
                            <input type="text" name="incident_location" value="{{ old('incident_location', $ob->incident_location ?? $case->arrest?->arrest_location) }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <hr style="border-color:#f3f4f6">
                    <h3 style="font-size:.85rem;font-weight:800;color:#374151">Complainant</h3>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Name</label>
                            <input type="text" name="complainant_name" value="{{ old('complainant_name', $ob->complainant_name ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Contact</label>
                            <input type="text" name="complainant_contact" value="{{ old('complainant_contact', $ob->complainant_contact ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">ID Number</label>
                            <input type="text" name="complainant_id" value="{{ old('complainant_id') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Relationship to Case</label>
                            <input type="text" name="complainant_relationship" value="{{ old('complainant_relationship', $ob->complainant_relationship ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <hr style="border-color:#f3f4f6">

                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Suspect Details</label>
                        <textarea name="suspect_details" rows="2" placeholder="{{ $case->arrest ? 'Linked from Stage 1: ' . $case->arrest->arrestee_name : '' }}"
                            style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('suspect_details', $ob->suspect_details ?? '') }}</textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Narrative</label>
                        <textarea name="narrative" rows="4"
                            style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('narrative', $ob->narrative ?? '') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Witnesses</label>
                            <textarea name="witnesses" rows="3"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('witnesses', $ob->witnesses ?? '') }}</textarea>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Initial Evidence Noted</label>
                            <textarea name="initial_evidence_noted" rows="3"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('initial_evidence_noted', $ob->initial_evidence_noted ?? '') }}</textarea>
                        </div>
                    </div>

                    <hr style="border-color:#f3f4f6">
                    <h3 style="font-size:.85rem;font-weight:800;color:#374151">Priority &amp; Assignment</h3>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Case Priority <span style="color:#ef4444">*</span></label>
                            <select name="priority" required style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                                @foreach(['Routine','Urgent','Critical'] as $p)
                                    <option value="{{ $p }}" {{ old('priority', $case->priority) == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Assign to Investigator</label>
                            <select name="assigned_investigator_id" style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                                <option value="">Select investigator</option>
                                @foreach($investigators as $inv)
                                    <option value="{{ $inv->id }}" {{ old('assigned_investigator_id', $ob->assigned_investigator_id ?? '') == $inv->id ? 'selected' : '' }}>{{ $inv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Assign to Unit</label>
                            <input type="text" name="assigned_unit" value="{{ old('assigned_unit', $ob->assigned_unit ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#374151">
                        <input type="checkbox" name="is_internal" value="1" {{ old('is_internal', $ob->is_internal ?? false) ? 'checked' : '' }}>
                        Internal OB (station-level incident, not from a public/external complaint)
                    </label>

                </div>

                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end">
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em">
                        <i class="bi bi-save-fill"></i> Save OB Entry
                    </button>
                </div>
            </form>

            @if($ob && !$ob->supervisor_acknowledged_at)
                <form action="{{ route('criminal-cases.workflow.ob.acknowledge', $case->id) }}" method="POST"
                      style="margin-top:1rem;padding-top:1.5rem;border-top:1px solid #f3f4f6">
                    @csrf
                    <p style="font-size:.8rem;color:#6b7280;margin-bottom:1rem">
                        A supervisor (Investigator or Institution Admin) must acknowledge this entry before Stage 3 unlocks.
                    </p>
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#16A34A;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em">
                        <i class="bi bi-patch-check-fill"></i> Supervisor Acknowledge
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>

@endsection
