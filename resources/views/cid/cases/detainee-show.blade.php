@extends('admin.admin_master')
@section('page_title', $detainee->detainee_name)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $detainee->detainee_name }}</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $detainee->detainee_number }} &mdash; {{ $detainee->criminalCase->case_number ?? '—' }} &mdash; admitted {{ $detainee->admission_datetime->format('Y-m-d H:i') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('criminal-cases.workflow', $detainee->criminal_case_id) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-folder2-open"></i> Open Case
            </a>
            @if($isAdmin)
                <a href="{{ route('cid-detainees.medical', $detainee->id) }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                    <i class="bi bi-clipboard2-pulse"></i> Medical Records
                </a>
            @endif
            <a href="{{ route('cid-detainees.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
                <i class="bi bi-arrow-left"></i> Back to Registry
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-neutral-100 p-5 lg:col-span-2">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Custody Details</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-neutral-400">Cell/Unit:</span> {{ $detainee->cell_unit_reference ?? '—' }}</div>
                <div><span class="text-neutral-400">Legal Deadline:</span> {{ $detainee->legal_deadline?->format('Y-m-d') ?? '—' }}</div>
                <div><span class="text-neutral-400">Court Order Ref:</span> {{ $detainee->court_order_reference ?? '—' }}</div>
                <div><span class="text-neutral-400">Admitting Officer:</span> {{ $detainee->admitting_officer }}</div>
            </div>
            @if($detainee->initial_health_declaration)
                <p class="text-sm text-neutral-600 mt-3"><span class="text-neutral-400">Health Declaration:</span> {{ $detainee->initial_health_declaration }}</p>
            @endif

            @if($isAdmin)
                <form action="{{ route('cid-detainees.status', $detainee->id) }}" method="POST" class="mt-4 pt-4 border-t border-neutral-100 flex gap-2">
                    @csrf
                    <select name="custody_status" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                        @foreach(\App\Models\CriminalDetainee::STATUSES as $s)
                            <option value="{{ $s }}" {{ $detainee->custody_status == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#528CBE">Update Status</button>
                </form>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-neutral-100 p-5">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Intake Checklist</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2"><i class="bi {{ $detainee->property_receipt_signed ? 'bi-check-circle-fill text-green-600' : 'bi-circle text-neutral-300' }}"></i> Property receipt signed</div>
                <div class="flex items-center gap-2"><i class="bi {{ $detainee->medical_screening_referred ? 'bi-check-circle-fill text-green-600' : 'bi-circle text-neutral-300' }}"></i> Medical screening referred</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-100 p-5 mb-6">
        <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Property Inventory</h3>
        <ul class="space-y-1 text-sm">
            @forelse($detainee->propertyItems as $item)
                <li class="flex items-center justify-between">
                    <span>{{ $item->item_description }}</span>
                    <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full {{ $item->returned ? 'bg-green-50 text-green-700' : 'bg-neutral-100 text-neutral-600' }}">
                        {{ $item->returned ? 'Returned' : 'Held' }}
                    </span>
                </li>
            @empty
                <li class="text-neutral-400">No property items recorded.</li>
            @endforelse
        </ul>
    </div>

    @if($isAdmin && !$detainee->release)
        <div class="bg-white rounded-2xl border border-neutral-100 p-5 mb-6">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Record Remand Order</h3>
            <form action="{{ route('cid-detainees.remand', $detainee->id) }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-2 items-end">
                    <input type="text" name="court_reference" placeholder="Court reference" required class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <input type="text" name="judge" placeholder="Judge" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <input type="text" name="remand_period" placeholder="e.g. 14 days" required class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <input type="date" name="remand_start_date" required class="text-sm px-3 py-2 border border-neutral-200 rounded-lg" title="Start date">
                    <input type="date" name="expiry_date" required class="text-sm px-3 py-2 border border-neutral-200 rounded-lg" title="Expiry date">
                </div>
                @if($detainee->remandOrders->isNotEmpty())
                    <select name="renewal_of" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg mt-2">
                        <option value="">Not a renewal</option>
                        @foreach($detainee->remandOrders as $ro)
                            <option value="{{ $ro->id }}">Renews order #{{ $ro->id }} ({{ $ro->expiry_date->format('Y-m-d') }})</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="mt-2 text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#528CBE">Record Remand Order</button>
            </form>
            @if($detainee->remandOrders->isNotEmpty())
                <ul class="mt-4 pt-4 border-t border-neutral-100 space-y-1 text-sm text-neutral-600">
                    @foreach($detainee->remandOrders as $ro)
                        <li>{{ $ro->court_reference }} &mdash; {{ $ro->remand_period }} &mdash; expires {{ $ro->expiry_date->format('Y-m-d') }}
                            @if($ro->renewal_of) <span class="text-neutral-400">(renews #{{ $ro->renewal_of }})</span> @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Record Transfer</h3>
                <form action="{{ route('cid-detainees.transfer', $detainee->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <input type="text" name="from_facility" placeholder="From facility" value="{{ $detainee->cell_unit_reference }}" required class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <input type="text" name="to_facility" placeholder="To facility" required class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <input type="datetime-local" name="transfer_datetime" required class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <input type="text" name="escorting_officer" placeholder="Escorting officer" required class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <textarea name="reason" placeholder="Reason" rows="2" class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg"></textarea>
                    <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#528CBE">Record Transfer</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Authorize Release</h3>
                <form action="{{ route('cid-detainees.release', $detainee->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <select name="release_type" required class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                        @foreach(\App\Models\CriminalDetaineeRelease::TYPES as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="authorizing_officer" placeholder="Authorizing officer" required class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <input type="text" name="release_document_reference" placeholder="Release document reference" class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <input type="datetime-local" name="released_at" required class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                    <label class="flex items-center gap-2 text-sm text-neutral-600"><input type="checkbox" name="property_returned_confirmed" value="1"> All property items returned</label>
                    <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#16A34A">Authorize Release</button>
                </form>
            </div>
        </div>
    @endif

    @if($detainee->transfers->isNotEmpty())
        <div class="bg-white rounded-2xl border border-neutral-100 p-5 mb-6">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Transfer History</h3>
            <ul class="space-y-2 text-sm">
                @foreach($detainee->transfers as $t)
                    <li>{{ $t->from_facility }} &rarr; {{ $t->to_facility }} on {{ $t->transfer_datetime->format('Y-m-d H:i') }} (escorted by {{ $t->escorting_officer }})</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($detainee->release)
        <div class="bg-white rounded-2xl border border-green-100 bg-green-50 p-5">
            <h3 style="font-size:.85rem;font-weight:800;color:#166534;margin-bottom:.5rem">Released</h3>
            <p class="text-sm text-green-700">{{ $detainee->release->release_type }} on {{ $detainee->release->released_at->format('Y-m-d H:i') }}, authorized by {{ $detainee->release->authorizing_officer }}.
                Property returned: {{ $detainee->release->property_returned_confirmed ? 'Yes' : 'No' }}.
            </p>
        </div>
    @endif

</div>

@endsection
