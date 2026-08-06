<?php

namespace App\Http\Controllers;

use App\Models\CriminalNumberFormat;
use Illuminate\Http\Request;

class CriminalNumberFormatController extends Controller
{
    private function isAdmin(Request $request): bool
    {
        return $request->user()->group?->roles->contains('name', 'CID Institution Admin') ?? false;
    }

    public function index(Request $request)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Number Formats is restricted to Institution Admin.');
        }

        $institutionId = $request->user()->institution_id;

        $formats = collect(CriminalNumberFormat::KEYS)->map(function ($meta, $key) use ($institutionId) {
            $config = CriminalNumberFormat::configFor($key, $institutionId);
            return [
                'key'     => $key,
                'label'   => $meta['label'],
                'config'  => $config,
                'preview' => CriminalNumberFormat::format($config, 1),
            ];
        })->values();

        return view('cid.settings.number-formats', compact('formats'));
    }

    public function update(Request $request, string $key)
    {
        if (!$this->isAdmin($request)) {
            abort(403, 'Number Formats is restricted to Institution Admin.');
        }

        if (!array_key_exists($key, CriminalNumberFormat::KEYS)) {
            abort(404);
        }

        $institutionId = $request->user()->institution_id;

        $data = $request->validate([
            'prefix'          => 'required|string|max:30',
            'include_year'    => 'nullable|boolean',
            'year_digits'     => 'required|in:2,4',
            'sequence_length' => 'required|integer|min:3|max:10',
            'reset_period'    => 'required|in:yearly,never',
            'override_reason' => 'nullable|string|max:255',
        ]);

        $existing = CriminalNumberFormat::where('institution_id', $institutionId)->where('format_key', $key)->first();

        if ($existing && $existing->locked && empty($data['override_reason'])) {
            return back()->withErrors(['override_reason' => 'This format is locked because numbers have already been issued. Provide an override reason to change it.'])->withInput();
        }

        $data['include_year'] = $request->boolean('include_year');
        $data['institution_id'] = $institutionId;
        $data['format_key'] = $key;
        $data['added_by'] = $request->user()->name ?? 'Staff';

        // An override intentionally keeps the format locked (it stays
        // locked for the *next* admin who wants to change it again) but
        // records why via the Auditable trait's automatic change log.
        if ($existing) {
            $existing->update($data);
        } else {
            CriminalNumberFormat::create($data);
        }

        return redirect()->route('cid-number-formats.index')->with('success', 'Number format saved.');
    }
}
