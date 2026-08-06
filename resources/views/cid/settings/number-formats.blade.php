@extends('admin.admin_master')
@section('page_title', 'Number Formats')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Number Formats</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Auto-generated reference number formats used across CID</p>
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

    <div class="space-y-4">
        @foreach($formats as $f)
            <div class="bg-white rounded-2xl border border-neutral-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 style="font-size:.9rem;font-weight:800;color:#374151">{{ $f['label'] }}</h3>
                    @if($f['config']->locked)
                        <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-amber-50 text-amber-700">
                            <i class="bi bi-lock-fill"></i> Locked (numbers already issued)
                        </span>
                    @endif
                </div>
                <form action="{{ route('cid-number-formats.update', $f['key']) }}" method="POST">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Prefix</label>
                            <input type="text" name="prefix" value="{{ old('prefix', $f['config']->prefix) }}" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Year Digits</label>
                            <select name="year_digits" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                                <option value="4" {{ $f['config']->year_digits == 4 ? 'selected' : '' }}>4-digit (2026)</option>
                                <option value="2" {{ $f['config']->year_digits == 2 ? 'selected' : '' }}>2-digit (26)</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Sequence Length</label>
                            <input type="number" name="sequence_length" min="3" max="10" value="{{ old('sequence_length', $f['config']->sequence_length) }}" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                        </div>
                        <div>
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Reset Period</label>
                            <select name="reset_period" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                                <option value="yearly" {{ $f['config']->reset_period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="never" {{ $f['config']->reset_period == 'never' ? 'selected' : '' }}>Never</option>
                            </select>
                        </div>
                    </div>
                    <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#374151;margin-bottom:1rem">
                        <input type="checkbox" name="include_year" value="1" {{ $f['config']->include_year ? 'checked' : '' }}> Include year in number
                    </label>

                    @if($f['config']->locked)
                        <div style="margin-bottom:1rem">
                            <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Override Reason (required to change a locked format)</label>
                            <input type="text" name="override_reason" placeholder="Why is this format changing mid-sequence?" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <p class="text-sm text-neutral-500">Preview: <span class="font-mono font-bold text-neutral-800">{{ $f['preview'] }}</span></p>
                        <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#528CBE">Save</button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>

</div>

@endsection
