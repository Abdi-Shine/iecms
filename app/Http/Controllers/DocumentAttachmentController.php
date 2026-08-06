<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentAttachmentController extends Controller
{
    public function index()
    {
        $courts = \App\Models\Court::orderBy('longName')->get();
        $query  = \App\Models\DocumentAttachment::with('court')->orderBy('Acode');

        if (auth()->check() && auth()->user()->position !== 'admin') {
            $userCourt = auth()->user()->employee->courtID ?? null;
            if ($userCourt) {
                // User sees attachments for their court OR those they added
                $query->where(function($q) use ($userCourt) {
                    $q->where('courtID', $userCourt)
                      ->orWhere('addedBy', auth()->user()->name);
                });
            } else {
                $query->where('addedBy', auth()->user()->name);
            }
        }

        $attachments = $query->get();
        return view('setting.document_attachement', compact('attachments', 'courts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Acode'   => 'required|string|max:50|unique:document_attachments,Acode',
            'Aname'   => 'required|string|max:100',
            'courtID' => 'nullable|string|max:50',
        ]);

        \App\Models\DocumentAttachment::create([
            'Acode'       => $request->Acode,
            'Aname'       => $request->Aname,
            'courtID'     => $request->courtID,
            'addedBy'     => auth()->user()->name ?? 'Admin',
            'addedDate'   => now()->format('Y-m-d'),
            'updatedBy'   => null,
            'updatedDate' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Document attachment created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Acode'   => 'required|string|max:50|unique:document_attachments,Acode,' . $id . ',AID',
            'Aname'   => 'required|string|max:100',
            'courtID' => 'nullable|string|max:50',
        ]);

        $attachment = \App\Models\DocumentAttachment::findOrFail($id);
        $attachment->update([
            'Acode'       => $request->Acode,
            'Aname'       => $request->Aname,
            'courtID'     => $request->courtID,
            'updatedBy'   => auth()->user()->name ?? 'Admin',
            'updatedDate' => now()->format('Y-m-d'),
        ]);

        return response()->json(['success' => true, 'message' => 'Document attachment updated successfully.']);
    }

    public function destroy($id)
    {
        $attachment = \App\Models\DocumentAttachment::findOrFail($id);
        $attachment->delete();

        return response()->json(['success' => true, 'message' => 'Document attachment deleted successfully.']);
    }

    public function export()
    {
        $attachments = \App\Models\DocumentAttachment::orderBy('Acode')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="document_attachments_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($attachments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Acode', 'Aname', 'courtID']);
            foreach ($attachments as $a) {
                fputcsv($handle, [$a->Acode, $a->Aname, $a->courtID ?? '']);
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
            if (count($row) < 2) { $skipped++; continue; }
            [$code, $name, $courtID] = array_pad($row, 3, '');
            $code = trim($code);
            if (!$code) { $skipped++; continue; }

            if (\App\Models\DocumentAttachment::where('Acode', $code)->exists()) {
                $skipped++;
                continue;
            }

            \App\Models\DocumentAttachment::create([
                'Acode'       => $code,
                'Aname'       => trim($name),
                'courtID'     => trim($courtID) ?: null,
                'addedBy'     => auth()->user()->name ?? 'Admin',
                'addedDate'   => now()->format('Y-m-d'),
                'updatedBy'   => null,
                'updatedDate' => null,
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('document-attachment.index')
            ->with('success', "Import complete: {$imported} added, {$skipped} skipped.");
    }
}
