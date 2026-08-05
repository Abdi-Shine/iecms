@extends('admin.admin_master')
@section('page_title', 'Exhibits — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Exhibit Management</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }}</p>
        </div>
        <a href="{{ route('criminal-cases.workflow', $case->id) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Case
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-100 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Log Exhibit</h3>
            <form action="{{ route('criminal-cases.exhibits.store', $case->id) }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:1rem;align-items:end">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Description</label>
                        <input type="text" name="description" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Receiving Officer</label>
                        <input type="text" name="receiving_officer" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Storage Location</label>
                        <input type="text" name="storage_location" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <button type="submit" style="padding:.65rem 1rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                        <i class="bi bi-box-seam"></i> Log
                    </button>
                </div>
                <div style="margin-top:1rem">
                    <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Condition</label>
                    <input type="text" name="condition" style="width:100%;max-width:400px;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Exhibit ID</th>
                    <th class="px-5 py-3">Description</th>
                    <th class="px-5 py-3">Storage</th>
                    <th class="px-5 py-3">Current Holder</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($case->exhibits as $ex)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">EXH-{{ str_pad($ex->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ex->description }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ex->storage_location ?? '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $ex->current_holder ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('criminal-cases.exhibits.status', [$case->id, $ex->id]) }}" method="POST" class="inline-flex gap-1">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="text-xs px-2 py-1 border border-neutral-200 rounded-lg">
                                    @foreach(\App\Models\CriminalCaseExhibit::STATUSES as $s)
                                        <option value="{{ $s }}" {{ $ex->status == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right text-neutral-400 text-xs">{{ $ex->condition ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-neutral-400">No exhibits logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
