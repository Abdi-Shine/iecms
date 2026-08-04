@extends('admin.admin_master')
@section('page_title', $formMeta['label'] . ' (' . $formMeta['code'] . ') — ' . $case->case_number)
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">{{ $formMeta['label'] }} ({{ $formMeta['code'] }})</h1>
            <p class="text-sm text-neutral-500 mt-0.5"><strong class="text-primary-400">{{ $case->case_number }}</strong>
                — {{ $case->title }}</p>
        </div>
        <a href="{{ route('attorney-cases.workflow', $case->ACID) }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 bg-white hover:bg-neutral-50 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i> Ku Laabo Workflow-ka
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center gap-2 bg-primary-400/4">
            <i class="bi bi-file-earmark-lock2 text-sm text-primary-400"></i>
            <span class="text-xs font-black uppercase tracking-[2px] text-neutral-500">Foomka Xaqiijinta</span>
        </div>

        <form action="{{ route('attorney-cases.compliance.store', ['id' => $case->ACID, 'type' => $type]) }}" method="POST" class="p-6">
            @csrf

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-white bg-danger-600">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5 items-stretch">

                {{-- Employee (read from Staff Registry) --}}
                <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(82,140,190,0.12)">
                        <i class="bi bi-person-badge text-lg" style="color:#528CBE"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-neutral-400 uppercase tracking-wider">Saxeexayaasha</p>
                        <p class="text-sm font-bold text-neutral-800 mt-0.5">
                            {{ $employee->EmpName ?? auth()->user()->name }}
                            <span class="font-medium text-neutral-500">— {{ $employee->Position ?? '—' }}</span>
                        </p>
                    </div>
                </div>

                {{-- Date + Confirmation --}}
                <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 flex flex-col justify-center gap-3">
                    <div>
                        <label class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-2 block">
                            Taariikhda Saxeexa <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="signed_date" required value="{{ old('signed_date', date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 text-sm border border-neutral-200 rounded-xl bg-white focus:outline-none focus:border-primary-400 transition-all">
                    </div>
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="confirm_details" value="1" {{ old('confirm_details', true) ? 'checked' : '' }}
                            class="w-4 h-4 mt-0.5 rounded border-neutral-300 text-primary-400 focus:ring-primary-400 cursor-pointer flex-shrink-0">
                        <span class="text-xs font-semibold text-neutral-600 leading-snug">Waan xaqiijinayaa faahfaahinta foomkan oo dhan ({{ $formMeta['label'] }}) <span class="text-danger-500">*</span></span>
                    </label>
                </div>

                {{-- Digital Signature — read from the employee's Staff Registry record, not re-captured here --}}
                <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
                    <p class="text-[10px] font-black text-neutral-500 uppercase tracking-wider mb-3">
                        Saxeexa Dijitalka ah <span class="text-danger-500">*</span>
                    </p>

                    @if($employee && $employee->signature)
                        <div class="rounded-xl border border-neutral-200 bg-white p-3">
                            <div class="h-14 flex items-center justify-center overflow-hidden">
                                <img src="{{ asset($employee->signature) }}" class="h-full w-auto max-w-full object-contain">
                            </div>
                            <div class="mt-2 pt-2 border-t border-neutral-100 flex items-center justify-center gap-1.5">
                                <i class="bi bi-patch-check-fill text-success-600 text-xs"></i>
                                <span class="text-xs font-bold text-neutral-700">Saxeexaaga oo Diiwaan ah</span>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl border p-3 flex items-start gap-2.5" style="background:#fff7ed;border-color:#fdba74">
                            <i class="bi bi-exclamation-triangle-fill text-sm mt-0.5" style="color:#ea580c"></i>
                            <div>
                                <p class="text-xs font-bold" style="color:#9a3412">Saxeex kuma jiro diiwaankaaga</p>
                                <p class="text-[11px] mt-0.5 leading-snug" style="color:#9a3412">
                                    Fadlan marka hore saxiixa ka geli bogga xogta shaqaalaha.
                                    @if($employee)
                                        <a href="{{ route('employee.edit', $employee->AID) }}" target="_blank" class="underline font-bold">Ku geli saxeexa</a>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="submit" {{ (!$employee || !$employee->signature) ? 'disabled' : '' }}
                    class="flex items-center gap-2 px-8 py-2.5 text-white text-sm font-bold rounded-xl shadow hover:opacity-90 transition-all bg-ago disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="bi bi-cloud-check-fill"></i>
                    <span>Keydi</span>
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
