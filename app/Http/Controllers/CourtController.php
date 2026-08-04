<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\CourtType;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function index(Request $request)
    {
        $query = Court::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('courtcode', 'like', "%$s%")
                  ->orWhere('shortName', 'like', "%$s%")
                  ->orWhere('longName',  'like', "%$s%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $this->resolvePerPage($request);

        $courts = $query->orderByDesc('CAI')->paginate($perPage)->withQueryString();

        $total = Court::count();
        $stats = [
            'total'    => $total,
            'active'   => Court::where('status', 'active')->count(),
            'inactive' => Court::where('status', 'inactive')->count(),
            'types'    => Court::distinct('type')->count('type'),
        ];

        $nextCode   = 'CRT' . str_pad($total + 1, 3, '0', STR_PAD_LEFT);
        $courtTypes = CourtType::where('status', 'active')->orderBy('name')->get();

        return view('Courts.setting.court_view', compact('courts', 'stats', 'nextCode', 'courtTypes'));
    }

    public function create()
    {
        $maxNum     = (int) (Court::selectRaw("MAX(CAST(SUBSTRING(courtcode, 4) AS UNSIGNED)) as m")->value('m') ?? 0);
        $nextCode   = 'CRT' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
        $courtTypes = CourtType::where('status', 'active')->orderBy('name')->get();

        return view('Courts.setting.court_add', compact('nextCode', 'courtTypes'));
    }

    public function edit(Court $court)
    {
        $courtTypes = CourtType::where('status', 'active')->orderBy('name')->get();

        return view('Courts.setting.court_edit', compact('court', 'courtTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'courtcode' => 'required|unique:courts,courtcode|max:20',
            'shortName' => 'required|max:20',
            'longName'  => 'required|max:100',
            'type'      => 'required',
        ]);

        $logo = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store('courts/logos', 'public');
        }

        $stamp = null;
        if ($request->hasFile('stamp')) {
            $stamp = $request->file('stamp')->store('courts/stamp', 'public');
        }

        $letterhead = null;
        if ($request->hasFile('letterhead')) {
            $letterhead = $request->file('letterhead')->store('courts/letterheads', 'public');
        }

        Court::create([
            'courtcode'  => $request->courtcode,
            'shortName'  => $request->shortName,
            'longName'   => $request->longName,
            'Grade'      => $request->Grade,
            'type'       => $request->type,
            'status'     => $request->status ?? 'active',
            'remarks'    => $request->remarks,
            'logo'       => $logo,
            'stamp'      => $stamp,
            'letterhead' => $letterhead,
            'address'    => $request->address,
            'telephone'  => $request->telephone,
            'email'      => $request->email,
            'addedBy'    => auth()->user()->name ?? 'Admin',
            'updatedBy'  => '',
            'updatedDate'=> '',
        ]);

        return redirect()->route('court.index')
                         ->with('success', 'Court registered successfully.');
    }

    public function update(Request $request, Court $court)
    {
        $request->validate([
            'shortName' => 'required|max:20',
            'longName'  => 'required|max:100',
            'type'      => 'required',
        ]);

        $data = [
            'shortName'   => $request->shortName,
            'longName'    => $request->longName,
            'Grade'       => $request->Grade,
            'type'        => $request->type,
            'status'      => $request->status,
            'remarks'     => $request->remarks,
            'address'     => $request->address,
            'telephone'   => $request->telephone,
            'email'       => $request->email,
            'website'     => $request->website,
            'updatedBy'   => auth()->user()->name ?? 'Admin',
            'updatedDate' => now()->toDateTimeString(),
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('courts/logos', 'public');
        }

        if ($request->hasFile('stamp')) {
            $data['stamp'] = $request->file('stamp')->store('courts/stamp', 'public');
        }

        if ($request->filled('letterhead_data')) {
            // PDF was converted to PNG client-side; decode base64 and save
            $dataUrl  = $request->input('letterhead_data');
            $base64   = substr($dataUrl, strpos($dataUrl, ',') + 1);
            $filename = 'courts/letterheads/' . uniqid('lh_') . '.png';
            \Storage::disk('public')->put($filename, base64_decode($base64));
            $data['letterhead'] = $filename;
        } elseif ($request->hasFile('letterhead')) {
            $data['letterhead'] = $request->file('letterhead')->store('courts/letterheads', 'public');
        }

        if ($request->input('arabic_name') !== null) {
            $data['arabic_name'] = $request->input('arabic_name');
        }

        $court->update($data);

        return redirect()->route('court.index')
                         ->with('success', 'Court updated successfully.');
    }

    public function destroy(Court $court)
    {
        $court->delete();

        return redirect()->route('court.index')
                         ->with('success', 'Court removed successfully.');
    }

    public function export()
    {
        $courts = Court::orderBy('courtcode')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="courts_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($courts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['courtcode', 'shortName', 'longName', 'type', 'status', 'telephone', 'email', 'address']);
            foreach ($courts as $c) {
                fputcsv($handle, [
                    $c->courtcode, $c->shortName, $c->longName,
                    $c->type, $c->status,
                    $c->telephone ?? '', $c->email ?? '', $c->address ?? '',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function import(\Illuminate\Http\Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        $handle   = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header   = fgetcsv($handle);
        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) { $skipped++; continue; }
            [$code, $short, $long, $type, $status, $tel, $email, $address] = array_pad($row, 8, '');
            $code = trim($code);
            if (!$code) { $skipped++; continue; }

            if (Court::where('courtcode', $code)->exists()) { $skipped++; continue; }

            Court::create([
                'courtcode'   => $code,
                'shortName'   => trim($short),
                'longName'    => trim($long),
                'type'        => trim($type) ?: 'General',
                'status'      => in_array(trim($status), ['active', 'inactive']) ? trim($status) : 'active',
                'telephone'   => trim($tel) ?: null,
                'email'       => trim($email) ?: null,
                'address'     => trim($address) ?: null,
                'addedBy'     => auth()->user()->name ?? 'Admin',
                'updatedBy'   => '',
                'updatedDate' => '',
            ]);
            $imported++;
        }
        fclose($handle);

        return redirect()->route('court.index')
            ->with('success', "Import complete: {$imported} added, {$skipped} skipped.");
    }
}
