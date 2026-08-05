@extends('admin.admin_master')
@section('page_title', 'Received Arrest Warrants')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Received Arrest Warrants</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Warrants received from courts/AGO directed to CID for execution</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Log Received Warrant</h3>
            <form action="{{ route('cid-received-warrants.store') }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Warrant Number</label>
                        <input type="text" name="warrant_number" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Issuing Authority</label>
                        <input type="text" name="issuing_authority" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Suspect Name</label>
                        <input type="text" name="suspect_name" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Offence</label>
                        <input type="text" name="offence" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Received Date</label>
                        <input type="date" name="received_date" required value="{{ date('Y-m-d') }}" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Warrant Expiry</label>
                        <input type="date" name="warrant_expiry_date" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <button type="submit" style="padding:.65rem 1.25rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                    <i class="bi bi-inbox-fill"></i> Log Warrant
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Warrant</th>
                    <th class="px-5 py-3">Suspect</th>
                    <th class="px-5 py-3">Issuing Authority</th>
                    <th class="px-5 py-3">Assigned Officer</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Expiry</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($warrants as $w)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $w->warrant_number }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $w->suspect_name }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $w->issuing_authority }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $w->assignedOfficer->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">{{ $w->execution_status }}</span>
                            @if($w->isNearingExpiry())
                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-red-50 text-red-700">Expiring Soon</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-neutral-500">{{ $w->warrant_expiry_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex gap-2 justify-end">
                                @if(!$w->assigned_officer_id)
                                    <form action="{{ route('cid-received-warrants.assign', $w->id) }}" method="POST" class="flex gap-1">
                                        @csrf
                                        <select name="assigned_officer_id" class="text-xs px-2 py-1 border border-neutral-200 rounded-lg">
                                            @foreach($officers as $o)
                                                <option value="{{ $o->id }}">{{ $o->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-xs font-bold px-2 py-1 rounded-lg text-white" style="background:#528CBE">Assign</button>
                                    </form>
                                @else
                                    <form action="{{ route('cid-received-warrants.status', $w->id) }}" method="POST" class="flex gap-1">
                                        @csrf
                                        <select name="execution_status" onchange="this.form.submit()" class="text-xs px-2 py-1 border border-neutral-200 rounded-lg">
                                            @foreach(\App\Models\CriminalReceivedWarrant::STATUSES as $s)
                                                <option value="{{ $s }}" {{ $w->execution_status == $s ? 'selected' : '' }}>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-neutral-400">No received warrants logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $warrants->links() }}</div>

</div>

@endsection
