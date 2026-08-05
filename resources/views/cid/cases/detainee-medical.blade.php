@extends('admin.admin_master')
@section('page_title', 'Medical Records — ' . $detainee->detainee_name)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Medical Records</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $detainee->detainee_name }} &mdash; restricted access</p>
        </div>
        <a href="{{ route('cid-detainees.show', $detainee->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Detainee
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Add Medical Visit</h3>
            <form action="{{ route('cid-detainees.medical.store', $detainee->id) }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Visit Date</label>
                        <input type="date" name="visit_date" required value="{{ date('Y-m-d') }}" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Visited By</label>
                        <input type="text" name="visited_by" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Referral To (if any)</label>
                        <input type="text" name="referral_to" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Screening Notes</label>
                        <textarea name="screening_notes" rows="2" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem"></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Ongoing Conditions / Medications</label>
                        <textarea name="ongoing_conditions" rows="2" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem"></textarea>
                    </div>
                </div>
                <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#374151;margin-bottom:1rem">
                    <input type="checkbox" name="is_emergency" value="1"> This is a medical emergency incident
                </label>
                <button type="submit" style="padding:.65rem 1.25rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                    <i class="bi bi-clipboard2-pulse"></i> Add Record
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($detainee->medicalRecords as $rec)
            <div class="bg-white rounded-2xl border {{ $rec->is_emergency ? 'border-red-200' : 'border-neutral-100' }} p-5">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="text-sm font-bold text-neutral-800">{{ $rec->visit_date->format('Y-m-d') }} &mdash; {{ $rec->visited_by }}</h4>
                    @if($rec->is_emergency)
                        <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-red-50 text-red-700">Emergency</span>
                    @endif
                </div>
                @if($rec->screening_notes)<p class="text-sm text-neutral-600"><strong>Notes:</strong> {{ $rec->screening_notes }}</p>@endif
                @if($rec->ongoing_conditions)<p class="text-sm text-neutral-600"><strong>Conditions:</strong> {{ $rec->ongoing_conditions }}</p>@endif
                @if($rec->referral_to)<p class="text-sm text-neutral-600"><strong>Referred to:</strong> {{ $rec->referral_to }}</p>@endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">No medical records yet.</div>
        @endforelse
    </div>

</div>

@endsection
