<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LawyerController extends Controller
{
    public function index(Request $request)
    {
        $query = Lawyer::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('LawyerName', 'like', '%' . $request->search . '%')
                  ->orWhere('LID', 'like', '%' . $request->search . '%')
                  ->orWhere('Email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('court')) {
            $query->where('CourtID', $request->court);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stats = [
            'total'  => (clone $query)->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'male'   => (clone $query)->where('Gender', 'Male')->count(),
            'female' => (clone $query)->where('Gender', 'Female')->count(),
        ];

        $perPage = $this->resolvePerPage($request);

        $lawyers = $query->orderBy('LRID', 'desc')->paginate($perPage)->withQueryString();
        $courts  = \App\Models\Court::orderBy('longName')->get();

        return view('Courts.lawyer.lawyer_view', compact('lawyers', 'courts', 'stats'));
    }

    public function create()
    {
        $courts    = \App\Models\Court::orderBy('longName')->get();
        $positions = ['Qareen', 'La-taliye Sharci'];
        return view('Courts.lawyer.lawyer_add', compact('courts', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'LawyerName' => 'required|string|max:100',
            'Gender'     => 'required',
            'CourtID'    => 'required',
            'Position'   => 'required',
            'status'     => 'required',
        ]);

        $data = $request->only([
            'LID', 'LawyerName', 'Phone', 'Email',
            'Gender', 'DOB', 'POB', 'CourtID', 'Position', 'Grade', 'status',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('lawyer_docements', 'public');
        }

        $data['addedBy']   = auth()->user()->name ?? 'Admin';
        $data['addedDate'] = now()->format('Y-m-d');

        Lawyer::create($data);

        return redirect()->route('lawyer.index')->with('success', 'Lawyer registered successfully.');
    }

    public function show($id)
    {
        $lawyer = Lawyer::findOrFail($id);
        return view('Courts.lawyer.lawyer_show', compact('lawyer'));
    }

    public function edit($id)
    {
        $lawyer    = Lawyer::findOrFail($id);
        $courts    = \App\Models\Court::orderBy('longName')->get();
        $positions = ['Qareen', 'La-taliye Sharci'];
        return view('Courts.lawyer.lawyer_edit', compact('lawyer', 'courts', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $lawyer = Lawyer::findOrFail($id);

        $request->validate([
            'LawyerName' => 'required|string|max:100',
            'Gender'     => 'required',
            'CourtID'    => 'required',
            'Position'   => 'required',
            'status'     => 'required',
        ]);

        $data = $request->only([
            'LID', 'LawyerName', 'Phone', 'Email',
            'Gender', 'DOB', 'POB', 'CourtID', 'Position', 'Grade', 'status',
        ]);

        if ($request->hasFile('photo')) {
            if ($lawyer->photo) Storage::disk('public')->delete($lawyer->photo);
            $data['photo'] = $request->file('photo')->store('lawyer_docements', 'public');
        }

        $data['updatedBy']   = auth()->user()->name ?? 'Admin';
        $data['updatedDate'] = now()->format('Y-m-d');

        $lawyer->update($data);

        return redirect()->route('lawyer.index')->with('success', 'Lawyer updated successfully.');
    }

    public function destroy($id)
    {
        $lawyer = Lawyer::findOrFail($id);
        if ($lawyer->photo) Storage::disk('public')->delete($lawyer->photo);
        $lawyer->delete();

        return redirect()->route('lawyer.index')->with('success', 'Lawyer deleted successfully.');
    }

    public function export()
    {
        $lawyers = Lawyer::orderBy('LRID')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="lawyers_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($lawyers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['LID', 'LawyerName', 'Phone', 'Email', 'Gender', 'DOB', 'POB', 'CourtID', 'Position', 'status']);
            foreach ($lawyers as $l) {
                fputcsv($handle, [$l->LID ?? '', $l->LawyerName, $l->Phone ?? '', $l->Email ?? '', $l->Gender, $l->DOB ?? '', $l->POB ?? '', $l->CourtID ?? '', $l->Position ?? '', $l->status]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        $handle   = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header   = fgetcsv($handle);
        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) { $skipped++; continue; }
            [$lid, $name, $phone, $email, $gender, $dob, $pob, $courtId, $position, $status] = array_pad($row, 10, '');
            $name = trim($name);
            if (!$name) { $skipped++; continue; }

            Lawyer::create([
                'LID'        => trim($lid) ?: null,
                'LawyerName' => $name,
                'Phone'      => trim($phone) ?: null,
                'Email'      => trim($email) ?: null,
                'Gender'     => trim($gender) ?: 'Male',
                'DOB'        => trim($dob) ?: null,
                'POB'        => trim($pob) ?: null,
                'CourtID'    => trim($courtId) ?: null,
                'Position'   => trim($position) ?: null,
                'status'     => trim($status) ?: 'active',
                'addedBy'    => auth()->user()->name ?? 'Admin',
                'addedDate'  => now()->format('Y-m-d'),
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('lawyer.index')
            ->with('success', "Import complete: {$imported} added, {$skipped} skipped.");
    }
}
