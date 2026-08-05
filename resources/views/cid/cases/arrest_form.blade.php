@extends('admin.admin_master')
@section('page_title', 'Stage 1 — Arrest')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Stage 1: Arrest</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; Warrant / Without Warrant</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <div style="padding:2rem 2.25rem">
            <form action="{{ route('criminal-cases.workflow.arrest.store', $case->id) }}" method="POST">
                @csrf
                @php $a = $case->arrest; @endphp

                <div style="display:flex;flex-direction:column;gap:1.5rem">

                    {{-- Arrest Type --}}
                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.55rem">
                            Arrest Type <span style="color:#ef4444">*</span>
                        </label>
                        <select name="arrest_type" id="arrestType" required onchange="toggleArrestFields()"
                            style="width:100%;max-width:320px;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                            <option value="">Select arrest type</option>
                            <option value="with_warrant" {{ old('arrest_type', $a->arrest_type ?? '') == 'with_warrant' ? 'selected' : '' }}>With Warrant</option>
                            <option value="without_warrant" {{ old('arrest_type', $a->arrest_type ?? '') == 'without_warrant' ? 'selected' : '' }}>Without Warrant</option>
                        </select>
                    </div>

                    {{-- Arrestee details --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Arrestee Full Name <span style="color:#ef4444">*</span></label>
                            <input type="text" name="arrestee_name" required value="{{ old('arrestee_name', $a->arrestee_name ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">National ID</label>
                            <input type="text" name="arrestee_national_id" value="{{ old('arrestee_national_id') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Date of Birth</label>
                            <input type="date" name="arrestee_dob" value="{{ old('arrestee_dob', optional($a?->arrestee_dob)->format('Y-m-d')) }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Gender</label>
                            <select name="arrestee_gender" style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                                <option value="">—</option>
                                <option value="Male" {{ old('arrestee_gender', $a->arrestee_gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('arrestee_gender', $a->arrestee_gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Nationality</label>
                            <input type="text" name="arrestee_nationality" value="{{ old('arrestee_nationality', $a->arrestee_nationality ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Contact</label>
                            <input type="text" name="arrestee_contact" value="{{ old('arrestee_contact', $a->arrestee_contact ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Address</label>
                        <input type="text" name="arrestee_address" value="{{ old('arrestee_address', $a->arrestee_address ?? '') }}"
                            style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                    </div>

                    <hr style="border-color:#f3f4f6">

                    {{-- Arresting officer --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Arresting Officer <span style="color:#ef4444">*</span></label>
                            <input type="text" name="arresting_officer_name" required value="{{ old('arresting_officer_name', $a->arresting_officer_name ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Badge Number</label>
                            <input type="text" name="arresting_officer_badge" value="{{ old('arresting_officer_badge', $a->arresting_officer_badge ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Unit</label>
                            <input type="text" name="arresting_officer_unit" value="{{ old('arresting_officer_unit', $a->arresting_officer_unit ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Arrest Date <span style="color:#ef4444">*</span></label>
                            <input type="date" name="arrest_date" required value="{{ old('arrest_date', optional($a?->arrest_date)->format('Y-m-d')) }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Arrest Time</label>
                            <input type="time" name="arrest_time" value="{{ old('arrest_time', $a->arrest_time ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Location</label>
                            <input type="text" name="arrest_location" value="{{ old('arrest_location', $a->arrest_location ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">GPS Coordinates</label>
                            <input type="text" name="arrest_gps" placeholder="lat, lng" value="{{ old('arrest_gps', $a->arrest_gps ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Alleged Offence <span style="color:#ef4444">*</span></label>
                            <input type="text" name="alleged_offence" required value="{{ old('alleged_offence', $a->alleged_offence ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Statute Reference</label>
                            <input type="text" name="statute_reference" value="{{ old('statute_reference', $a->statute_reference ?? '') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <hr style="border-color:#f3f4f6">

                    {{-- Warrant details --}}
                    <div id="warrantFields" style="display:none">
                        <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Warrant Details</h3>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
                            <div>
                                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Warrant Number <span style="color:#ef4444">*</span></label>
                                <input type="text" name="warrant_number" value="{{ old('warrant_number', $a->warrant_number ?? '') }}"
                                    style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                            </div>
                            <div>
                                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Issuing Court</label>
                                <input type="text" name="warrant_issuing_court" value="{{ old('warrant_issuing_court', $a->warrant_issuing_court ?? '') }}"
                                    style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                            </div>
                            <div>
                                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Issuing Judge</label>
                                <input type="text" name="warrant_issuing_judge" value="{{ old('warrant_issuing_judge', $a->warrant_issuing_judge ?? '') }}"
                                    style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                            <div>
                                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Issue Date</label>
                                <input type="date" name="warrant_issue_date" value="{{ old('warrant_issue_date', optional($a?->warrant_issue_date)->format('Y-m-d')) }}"
                                    style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                            </div>
                            <div>
                                <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Expiry Date</label>
                                <input type="date" name="warrant_expiry_date" value="{{ old('warrant_expiry_date', optional($a?->warrant_expiry_date)->format('Y-m-d')) }}"
                                    style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                            </div>
                        </div>
                    </div>

                    {{-- Without warrant justification --}}
                    <div id="warrantlessFields" style="display:none">
                        <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Without Warrant Justification</h3>
                        <div style="margin-bottom:1.5rem">
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Reason <span style="color:#ef4444">*</span></label>
                            <textarea name="warrantless_justification" rows="3"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('warrantless_justification', $a->warrantless_justification ?? '') }}</textarea>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Witnesses (if any)</label>
                            <textarea name="warrantless_witnesses" rows="2"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('warrantless_witnesses', $a->warrantless_witnesses ?? '') }}</textarea>
                        </div>
                    </div>

                    <hr style="border-color:#f3f4f6">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Physical Condition at Arrest</label>
                            <textarea name="physical_condition" rows="2"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('physical_condition', $a->physical_condition ?? '') }}</textarea>
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Items Seized (Preliminary)</label>
                            <textarea name="items_seized_preliminary" rows="2"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">{{ old('items_seized_preliminary', $a->items_seized_preliminary ?? '') }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Detention Location After Arrest</label>
                        <input type="text" name="detention_location_after_arrest" value="{{ old('detention_location_after_arrest', $a->detention_location_after_arrest ?? '') }}"
                            style="width:100%;max-width:400px;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                    </div>

                </div>

                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end">
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em">
                        <i class="bi bi-check-circle-fill"></i> Submit Arrest Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleArrestFields() {
        const type = document.getElementById('arrestType').value;
        document.getElementById('warrantFields').style.display = type === 'with_warrant' ? 'block' : 'none';
        document.getElementById('warrantlessFields').style.display = type === 'without_warrant' ? 'block' : 'none';
    }
    document.addEventListener('DOMContentLoaded', toggleArrestFields);
</script>

@endsection
