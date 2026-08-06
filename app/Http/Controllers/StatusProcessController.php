<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatusProcessController extends Controller
{
    public function index()
    {
        $statuses = \App\Models\StatusProcess::orderBy('status_code')->get();
        return view('setting.status_process', compact('statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_code' => 'required|string|max:50|unique:status_processes,status_code',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        \App\Models\StatusProcess::create($request->only('status_code', 'name', 'description'));

        return response()->json(['success' => true, 'message' => 'Status process created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_code' => 'required|string|max:50|unique:status_processes,status_code,' . $id,
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $status = \App\Models\StatusProcess::findOrFail($id);
        $status->update($request->only('status_code', 'name', 'description'));

        return response()->json(['success' => true, 'message' => 'Status process updated successfully.']);
    }

    public function destroy($id)
    {
        $status = \App\Models\StatusProcess::findOrFail($id);
        $status->delete();

        return response()->json(['success' => true, 'message' => 'Status process deleted successfully.']);
    }

    public function export()
    {
        $statuses = \App\Models\StatusProcess::orderBy('status_code')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="status_processes_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($statuses) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['status_code', 'name', 'description']);
            foreach ($statuses as $s) {
                fputcsv($handle, [$s->status_code, $s->name, $s->description ?? '']);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) { $skipped++; continue; }
            [$code, $name, $desc] = array_pad($row, 3, '');
            $code = trim($code);
            if (!$code) { $skipped++; continue; }

            $exists = \App\Models\StatusProcess::where('status_code', $code)->exists();
            if ($exists) { $skipped++; continue; }

            \App\Models\StatusProcess::create([
                'status_code' => $code,
                'name'        => trim($name),
                'description' => trim($desc) ?: null,
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('status-process.index')
            ->with('success', "Import complete: {$imported} added, {$skipped} skipped.");
    }
}
