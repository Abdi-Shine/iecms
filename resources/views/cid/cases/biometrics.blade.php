@extends('admin.admin_master')
@section('page_title', 'Biometrics — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Biometrics</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $case->case_number }} &mdash; reference numbers only, no raw biometric data stored</p>
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
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Capture Biometric Record</h3>
            <form action="{{ route('criminal-cases.biometrics.store', $case->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Person Name</label>
                        <input type="text" name="person_name" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Role</label>
                        <select name="person_role" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            <option value="suspect">Suspect</option>
                            <option value="person_of_interest">Person of Interest</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Facial Photo</label>
                        <input type="file" name="facial_photo" accept="image/*" style="width:100%;font-size:.78rem">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Fingerprint Reference</label>
                        <input type="text" name="fingerprint_reference" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">DNA Reference</label>
                        <input type="text" name="dna_reference" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Iris Scan Reference</label>
                        <input type="text" name="iris_scan_reference" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;align-items:end">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Captured By</label>
                        <input type="text" name="captured_by" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Captured Date</label>
                        <input type="date" name="captured_date" required value="{{ date('Y-m-d') }}" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <button type="submit" style="padding:.65rem 1rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                        <i class="bi bi-fingerprint"></i> Capture
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($case->biometrics as $b)
            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        @if($b->facial_photo_path)
                            <img src="{{ asset($b->facial_photo_path) }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-neutral-100 flex items-center justify-center"><i class="bi bi-person text-neutral-400"></i></div>
                        @endif
                        <div>
                            <h4 class="text-sm font-bold text-neutral-800">{{ $b->person_name }}</h4>
                            <p class="text-xs text-neutral-500">{{ ucfirst(str_replace('_',' ',$b->person_role)) }} &middot; captured {{ $b->captured_date->format('Y-m-d') }} by {{ $b->captured_by }}</p>
                        </div>
                    </div>
                    @php
                        $mColor = match($b->match_status) {
                            'Match Found' => ['#DCFCE7','#16A34A'], 'No Match' => ['#F3F4F6','#6B7280'],
                            'Inconclusive' => ['#FEF3C7','#B45309'], default => ['#F3F4F6','#6B7280']
                        };
                    @endphp
                    <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full" style="background:{{ $mColor[0] }};color:{{ $mColor[1] }}">{{ $b->match_status }}</span>
                </div>

                <div class="grid grid-cols-3 gap-3 text-xs text-neutral-600 mb-3">
                    <div><span class="text-neutral-400">Fingerprint:</span> {{ $b->fingerprint_reference ?? '—' }}</div>
                    <div><span class="text-neutral-400">DNA:</span> {{ $b->dna_reference ?? '—' }}</div>
                    <div><span class="text-neutral-400">Iris:</span> {{ $b->iris_scan_reference ?? '—' }}</div>
                </div>

                <form action="{{ route('criminal-cases.biometrics.match', [$case->id, $b->id]) }}" method="POST" class="flex gap-2">
                    @csrf
                    <select name="match_status" class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg">
                        @foreach(\App\Models\CriminalCaseBiometric::MATCH_STATUSES as $s)
                            <option value="{{ $s }}" {{ $b->match_status == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="match_reference" placeholder="Match reference / notes" value="{{ $b->match_reference }}" class="text-xs px-3 py-1.5 border border-neutral-200 rounded-lg flex-1">
                    <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#528CBE">Update</button>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-neutral-100 p-10 text-center text-neutral-400">No biometric records captured yet.</div>
        @endforelse
    </div>

</div>

@endsection
