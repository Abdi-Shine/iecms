@extends('admin.admin_master')
@section('page_title', 'New Admission — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">New Detention Admission</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; {{ $case->arrest->arrestee_name ?? '' }}</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Case
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
            <form action="{{ route('cid-detainees.admit', $case->id) }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:1.5rem">

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Admission Date/Time <span style="color:#ef4444">*</span></label>
                            <input type="datetime-local" name="admission_datetime" required value="{{ now()->format('Y-m-d\TH:i') }}"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Admitting Officer <span style="color:#ef4444">*</span></label>
                            <input type="text" name="admitting_officer" required
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Cell/Unit Assignment</label>
                            <input type="text" name="cell_unit_reference"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Legal Deadline (Remand Expiry)</label>
                            <input type="date" name="legal_deadline"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Court Order Reference</label>
                            <input type="text" name="court_order_reference"
                                style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem">
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Initial Health Declaration</label>
                        <textarea name="initial_health_declaration" rows="3"
                            style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem"></textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.55rem">Property Inventory (one item per line)</label>
                        <textarea name="property_items_raw" id="propertyItemsRaw" rows="3" oninput="syncPropertyItems()"
                            placeholder="e.g. Mobile phone&#10;Wallet with $40&#10;Watch"
                            style="width:100%;padding:.75rem 1rem;font-size:.875rem;border:1.5px solid #d1d5db;border-radius:.625rem"></textarea>
                        <div id="propertyItemsHidden"></div>
                    </div>

                    <div style="display:flex;gap:2rem">
                        <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#374151">
                            <input type="checkbox" name="property_receipt_signed" value="1"> Property receipt signed
                        </label>
                        <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#374151">
                            <input type="checkbox" name="medical_screening_referred" value="1"> Medical screening referred
                        </label>
                    </div>

                </div>

                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end">
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:.6rem;padding:.75rem 2.25rem;font-size:.82rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.75rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em">
                        <i class="bi bi-shield-lock-fill"></i> Admit Detainee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function syncPropertyItems() {
        const lines = document.getElementById('propertyItemsRaw').value.split('\n').filter(l => l.trim());
        const container = document.getElementById('propertyItemsHidden');
        container.innerHTML = '';
        lines.forEach(line => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'property_items[]';
            input.value = line.trim();
            container.appendChild(input);
        });
    }
    document.querySelector('form').addEventListener('submit', syncPropertyItems);
</script>

@endsection
