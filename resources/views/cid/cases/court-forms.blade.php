@extends('admin.admin_master')
@section('page_title', 'Court Forms — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Court Appearance Forms</h1>
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
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Generate Form</h3>
            <form action="{{ route('criminal-cases.court-forms.store', $case->id) }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1rem;align-items:end">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Form Type</label>
                        <select name="form_type" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            @foreach(\App\Models\CriminalCourtAppearanceForm::TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Recipient Name</label>
                        <input type="text" name="recipient_name" required style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Recipient Role</label>
                        <input type="text" name="recipient_role" placeholder="Witness / Officer" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;margin-bottom:.4rem">Related Hearing</label>
                        <select name="court_appearance_id" style="width:100%;padding:.6rem .8rem;font-size:.82rem;border:1.5px solid #d1d5db;border-radius:.5rem">
                            <option value="">None</option>
                            @foreach($case->courtAppearances as $ca)
                                <option value="{{ $ca->id }}">{{ $ca->hearing_type }} — {{ $ca->appearance_date->format('Y-m-d') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" style="margin-top:1rem;padding:.65rem 1.25rem;font-size:.78rem;font-weight:800;color:white;background:#528CBE;border:none;border-radius:.5rem;cursor:pointer;text-transform:uppercase">
                    <i class="bi bi-file-earmark-plus"></i> Generate
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                    <th class="px-5 py-3">Form Type</th>
                    <th class="px-5 py-3">Recipient</th>
                    <th class="px-5 py-3">Hearing</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($case->courtAppearanceForms as $form)
                    <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                        <td class="px-5 py-3 font-semibold text-neutral-800">{{ \App\Models\CriminalCourtAppearanceForm::TYPES[$form->form_type] }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $form->recipient_name }} @if($form->recipient_role) ({{ $form->recipient_role }}) @endif</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $form->courtAppearance?->hearing_type ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-neutral-100 text-neutral-600">{{ $form->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form action="{{ route('criminal-cases.court-forms.status', [$case->id, $form->id]) }}" method="POST" class="inline-flex gap-1">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="text-xs px-2 py-1 border border-neutral-200 rounded-lg">
                                    @foreach(\App\Models\CriminalCourtAppearanceForm::STATUSES as $s)
                                        <option value="{{ $s }}" {{ $form->status == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-neutral-400">No forms generated yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
