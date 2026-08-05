@extends('admin.admin_master')
@section('page_title', $event->event_name)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $event->event_name }}</h1>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $event->event_date->format('Y-m-d') }} &middot; {{ $event->location }} &middot; Commanded by {{ $event->commanding_officer }}</p>
        </div>
        <a href="{{ route('cid-bulk-arrests.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Back to Events
        </a>
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

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 mb-6">
        <div style="padding:1.5rem 2rem">
            <h3 style="font-size:.85rem;font-weight:800;color:#374151;margin-bottom:1rem">Add Arrestee</h3>
            <form action="{{ route('cid-bulk-arrests.members.store', $event->id) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="arrestee_name" placeholder="Arrestee name" required class="text-sm px-3 py-2 border border-neutral-200 rounded-lg flex-1">
                <input type="text" name="alleged_offence" placeholder="Alleged offence" required class="text-sm px-3 py-2 border border-neutral-200 rounded-lg flex-1">
                <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-lg text-white" style="background:#528CBE">Add</button>
            </form>
        </div>
    </div>

    <form action="{{ route('cid-bulk-arrests.assign', $event->id) }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-neutral-100 p-3 mb-4 flex items-center gap-3">
            <select name="assigned_investigator_id" class="text-sm px-3 py-2 border border-neutral-200 rounded-lg">
                <option value="">Assign selected to investigator...</option>
                @foreach($investigators as $inv)
                    <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="text-sm font-semibold px-3 py-2 rounded-lg text-white" style="background:#528CBE">Bulk Assign</button>
        </div>

        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                        <th class="px-5 py-3 w-8"></th>
                        <th class="px-5 py-3">Arrestee</th>
                        <th class="px-5 py-3">Alleged Offence</th>
                        <th class="px-5 py-3">Investigator</th>
                        <th class="px-5 py-3">Case</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($event->members as $member)
                        <tr class="border-b border-neutral-50 hover:bg-neutral-50/50">
                            <td class="px-5 py-3"><input type="checkbox" name="member_ids[]" value="{{ $member->id }}"></td>
                            <td class="px-5 py-3 font-semibold text-neutral-800">{{ $member->arrestee_name }}</td>
                            <td class="px-5 py-3 text-neutral-600">{{ $member->alleged_offence }}</td>
                            <td class="px-5 py-3 text-neutral-600">{{ $member->assignedInvestigator->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-neutral-600">{{ $member->criminalCase->case_number ?? '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                @if($member->criminal_case_id)
                                    <a href="{{ route('criminal-cases.workflow', $member->criminal_case_id) }}"
                                       class="text-[13px] font-semibold" style="color:#528CBE">Open Case &rarr;</a>
                                @else
                                    <button type="submit" formaction="{{ route('cid-bulk-arrests.generate-case', [$event->id, $member->id]) }}"
                                        class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#16A34A">Generate Case</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-neutral-400">No arrestees added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

</div>

@endsection
