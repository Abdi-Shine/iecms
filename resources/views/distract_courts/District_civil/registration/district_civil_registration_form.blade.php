@extends('admin.admin_master')
@section('page_title', isset($record) ? 'Edit Civil Case — Civil Registry' : 'Diiwanka Dacwada Madaniga — Civil Registry')
@section('admin_main_content')

@php
    $isEditMode = isset($record);
@endphp

<div class="p-4 sm:p-6 w-full" x-data="{
        gradeCourt: '{{ old('GradeCourt', $record->GradeCourt ?? $userCourtID ?? '') }}',
        fileNo: '{{ old('FileNo', $record->FileNo ?? '') }}',
        isEditMode: {{ $isEditMode ? 'true' : 'false' }},
        async generateFileNo(courtcode) {
            if (!courtcode || this.isEditMode) return;
            this.fileNo = '';
            try {
                const res = await fetch(`/civil-registration/next-fileno/${encodeURIComponent(courtcode)}`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (data.fileNo) this.fileNo = data.fileNo;
            } catch (e) { console.error('generateFileNo failed:', e); }
        }
    }"
    x-init="if (gradeCourt && !isEditMode) generateFileNo(gradeCourt)">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">
                {{ $isEditMode ? 'Edit Civil Case' : 'Diiwanka Dacwada Madaniga' }}
            </h1>
            <p class="text-sm text-neutral-500 mt-0.5">Buuxi dhammaan goobaha loo baahdo</p>
        </div>
        <a href="{{ route('civil-registration.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i> Ku Laabo Diiwanka
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2"
            style="background:{{ $isEditMode ? 'rgba(240,180,60,0.06)' : 'rgba(82,140,190,0.06)' }}">
            <i class="bi bi-{{ $isEditMode ? 'pencil-square' : 'file-earmark-plus' }} text-sm"
                style="color:{{ $isEditMode ? '#F0B43C' : '#528CBE' }}"></i>
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Foomka Diiwaan Gelinta</span>
        </div>

        <form action="{{ $isEditMode ? route('civil-registration.update', $record->CRID) : route('civil-registration.store') }}"
            method="POST" class="p-6">
            @csrf
            @if($isEditMode)
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-white bg-danger-600">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Row 1: B.G Lambar | Magaca Maxkamadda | Gal Lambar --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="cl">B.G Lambar <span class="text-danger-500">*</span></label>
                    <input type="text" name="RegisterNo" required placeholder="tusaale: BG-2024-001"
                        value="{{ old('RegisterNo', $record->RegisterNo ?? '') }}" class="ci">
                </div>
                <div>
                    <label class="cl">Magaca Maxkamadda <span class="text-danger-500">*</span></label>
                    <select name="GradeCourt" required x-model="gradeCourt" @change="generateFileNo(gradeCourt)"
                        {{ (!$isEditMode && auth()->user()->position !== 'admin' && $userCourtID) ? 'disabled' : '' }}
                        class="ci">
                        <option value="">— Dooro Maxkamadda —</option>
                        @foreach($courts as $c)
                            <option value="{{ $c->courtcode }}">{{ $c->longName }}</option>
                        @endforeach
                    </select>
                    {{-- Disabled selects don't submit — mirror the locked value so it still posts. --}}
                    @if(!$isEditMode && auth()->user()->position !== 'admin' && $userCourtID)
                        <input type="hidden" name="GradeCourt" value="{{ old('GradeCourt', $userCourtID) }}">
                    @endif
                </div>
                <div>
                    <label class="cl">Gal Lambar <span class="text-danger-500">*</span></label>
                    <input type="text" name="FileNo" required readonly placeholder="Xulo Maxkamadda..."
                        x-model="fileNo" class="ci" style="background:#f9fafb;color:#9ca3af;cursor:not-allowed">
                </div>
            </div>

            {{-- Gal Lambar Preview --}}
            <div x-show="fileNo && !isEditMode" x-cloak class="mb-5">
                <div class="rounded-2xl border p-4 flex items-center gap-3" style="background:linear-gradient(135deg,#eef4fb,#e8f0f9);border-color:#c3d9f0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:#528CBE">
                        <i class="bi bi-info-lg text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" style="color:#0A284D">
                            Lambarka Dacwada Madaniga:&nbsp;<span x-text="fileNo" class="text-base font-black tracking-wide" style="color:#0A284D"></span>
                        </p>
                        <p class="text-xs mt-0.5" style="color:#528CBE">
                            Tani waa muuqaal hore oo lambarka la siin doono marka foomka la gudbinayo.
                        </p>
                    </div>
                </div>
            </div>

            <input type="hidden" name="CaseType" value="Madani">

            {{-- Nooca Hoosaadka Dacwadda --}}
            <div class="mb-5">
                <label class="cl">Nooca Hoosaadka Dacwadda</label>
                <select name="SubCase" class="ci">
                    <option value="">— Dooro Nooca Hoosaadka —</option>
                    @foreach($civilSubCases as $sub)
                        <option value="{{ $sub }}" {{ old('SubCase', $record->SubCase ?? '') === $sub ? 'selected' : '' }}>{{ $sub }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Row 2: Taariikhda Furitaanka | Xaaladda | Lambarka Warqadda --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="cl">Taariikhda Furitaanka <span class="text-danger-500">*</span></label>
                    <input type="date" name="OpenDate" required
                        value="{{ old('OpenDate', isset($record) ? $record->OpenDate : date('Y-m-d')) }}" class="ci">
                </div>
                <div>
                    <label class="cl">Xaaladda</label>
                    <select name="Status" required class="ci" style="background:#f9fafb;pointer-events:none">
                        <option value="Diwaan Galin" selected>Diwaan Galin</option>
                    </select>
                </div>
                <div>
                    <label class="cl">Lambarka Warqadda</label>
                    <input type="text" name="NumberLetter" placeholder="tusaale: WRQ-001"
                        value="{{ old('NumberLetter', $record->NumberLetter ?? '') }}" class="ci">
                </div>
            </div>

            <input type="hidden" name="LegalBasis" value="Dacwadda Madaniga">

            {{-- Codsiga Dacwoodaha --}}
            <div class="mb-5">
                <label class="cl">Codsiga Dacwoodaha</label>
                <textarea name="Orders_Requested" rows="3" placeholder="Sharax amarrada la codsanayo..."
                    class="ci">{{ old('Orders_Requested', $record->Orders_Requested ?? '') }}</textarea>
            </div>

            {{-- Mooduca Dacwadda --}}
            <div class="mb-6">
                <label class="cl">Mooduca Dacwadda</label>
                <textarea name="Remarks" rows="3" placeholder="Faallo dheeraad ah..."
                    class="ci">{{ old('Remarks', $record->Remarks ?? '') }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                <a href="{{ route('civil-registration.index') }}"
                    class="px-6 py-2.5 text-sm font-semibold text-neutral-600 border border-neutral-200 rounded-xl bg-white hover:bg-neutral-50 transition-all">
                    Jooji
                </a>
                <button type="submit"
                    class="flex items-center gap-2 px-7 py-2.5 text-white text-sm font-bold rounded-xl shadow transition-all hover:opacity-90"
                    style="background:{{ $isEditMode ? '#F0B43C' : '#528CBE' }}">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ $isEditMode ? 'Kaydi Isbeddelada' : 'Kaydi' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
