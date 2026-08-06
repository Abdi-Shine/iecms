@extends('admin.admin_master')
@section('page_title', 'Create New Ticket')
@section('admin_main_content')

    <div class="p-4 sm:p-6 w-full">

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('support-tickets.index') }}"
                class="w-9 h-9 rounded-lg border border-neutral-200 flex items-center justify-center text-neutral-500 hover:bg-neutral-50 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Create New Ticket</h1>
                <p class="text-sm text-neutral-500 mt-0.5">Describe your issue and our team will get back to you.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-5 px-4 py-3 rounded-xl bg-danger-50 border border-danger-200 text-danger-700 text-sm font-medium">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('support-tickets.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                    placeholder="Brief summary of the issue"
                    class="w-full rounded-lg border border-neutral-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Category</label>
                    <select name="category" required class="w-full rounded-lg border border-neutral-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                        <option value="" disabled selected>Select category</option>
                        @foreach($categories as $c)
                            <option value="{{ $c }}" @selected(old('category') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Priority</label>
                    <select name="priority" required class="w-full rounded-lg border border-neutral-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                        <option value="" disabled selected>Select priority</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p }}" @selected(old('priority') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Description</label>
                <textarea name="description" rows="6" required placeholder="Explain the issue in detail..."
                    class="w-full rounded-lg border border-neutral-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Attachments</label>
                <input type="file" name="attachments[]" multiple
                    class="w-full rounded-lg border border-neutral-200 px-3 py-2.5 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary file:font-semibold file:text-xs hover:file:bg-primary-100">
                <p class="text-xs text-neutral-400 mt-1.5">Up to 5 files, 10MB each. Images, PDF, Word, Excel, text, or zip.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('support-tickets.index') }}"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-neutral-600 hover:bg-neutral-50 transition-colors">Cancel</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl shadow hover:opacity-90 transition-all">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
@endsection
