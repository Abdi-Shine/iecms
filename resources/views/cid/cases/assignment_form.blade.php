@extends('admin.admin_master')
@section('page_title', 'Stage 3 — Case Assignment')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Stage 3: Case Assignment</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; Formal assignment &amp; investigation plan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('criminal-cases.workflow.evidence.index', $case->id) }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-archive"></i> Evidence ({{ $case->evidenceItems->count() }})
            </a>
            <a href="{{ route('criminal-cases.workflow', $case->id) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
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

    @php $a = $case->assignment; @endphp

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <div style="padding:2rem 2.25rem">
            <form action="{{ route('criminal-cases.workflow.assignment.store', $case->id) }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:1.5rem">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Assigned Investigator <span style="color:#ef4444">*</span></label>
                            <select name="assigned_investigator_id" required style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                                <option value="">Select investigator</option>
                                @foreach($investigators as $inv)
                                    <option value="{{ $inv->id }}" {{ old('assigned_investigator_id', $a->assigned_investigator_id ?? $case->occurrenceBook->assigned_investigator_id ?? '') == $inv->id ? 'selected' : '' }}>{{ $inv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Secondary Officers</label>
                            <input type="text" name="secondary_officers" value="{{ old('secondary_officers', $a->secondary_officers ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Investigation Plan / Strategy Notes</label>
                        <textarea name="investigation_plan" rows="4"
                            style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('investigation_plan', $a->investigation_plan ?? '') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Investigation Start Date <span style="color:#ef4444">*</span></label>
                            <input type="date" name="investigation_start_date" required value="{{ old('investigation_start_date', optional($a?->investigation_start_date)->format('Y-m-d') ?? date('Y-m-d')) }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Target Completion Date</label>
                            <input type="date" name="target_completion_date" value="{{ old('target_completion_date', optional($a?->target_completion_date)->format('Y-m-d')) }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                </div>

                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end">
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em">
                        <i class="bi bi-check-circle-fill"></i> Save Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
