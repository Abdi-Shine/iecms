@extends('admin.admin_master')
@section('page_title', 'Gal Qorista Cabashada — Direct Complaint')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Gal Qorista Cabashada</h1>
            <p class="text-sm text-neutral-500 mt-0.5">Qoritaanka iyo u qoondaynta dacwadaha cabashada toosan ah</p>
        </div>
        <a href="{{ route('attorney-cases.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-50 transition">
            <i class="bi bi-arrow-left"></i> Diiwaanka Cabashada
        </a>
    </div>

    {{-- Placeholder --}}
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-16 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background:rgba(82,140,190,0.1)">
            <i class="bi bi-clipboard-plus text-3xl" style="color:#528CBE"></i>
        </div>
        <h2 class="text-lg font-bold text-neutral-800">Gal Qorista Cabashada</h2>
        <p class="text-sm text-neutral-500 mt-1 max-w-md">Boggan waa mid la horumarinayo. Halkan ayaa lagu qori doonaa oo lagu qoondayn doonaa dacwadaha cabashada toosan ah xeer ilaaliyaha khuseeya.</p>
    </div>

</div>
@endsection
