@extends('admin.admin_master')
@section('page_title', 'Bulk Arrest Management')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Bulk Arrest Management</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Raids, mass arrests, and public order events</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">New Bulk Arrest Event</h3>
            <form action="{{ route('cid-bulk-arrests.store') }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Event Name</label>
                        <input type="text" name="event_name" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Date</label>
                        <input type="date" name="event_date" required value="{{ date('Y-m-d') }}" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Location</label>
                        <input type="text" name="location" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:end">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Operation Reference</label>
                        <input type="text" name="operation_reference" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Commanding Officer</label>
                        <input type="text" name="commanding_officer" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <button type="submit" style="margin-top:1rem;padding:.65rem 1.25rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                    <i class="bi bi-plus-lg"></i> Create Event
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Event</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Location</th>
                    <th class="px-5 py-3">Commanding Officer</th>
                    <th class="px-5 py-3">Arrestees</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ $event->event_name }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $event->event_date->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $event->location ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $event->commanding_officer }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $event->members_count }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('cid-bulk-arrests.show', $event->id) }}"
                               class="text-[13px] font-semibold" style="color:#528CBE">Open &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-neutral-400">No bulk arrest events yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $events->links() }}</div>

</div>

@endsection
