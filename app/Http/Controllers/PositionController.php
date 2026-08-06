<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('Pname', 'like', "%$s%")
                  ->orWhere('Pcode', 'like', "%$s%");
            });
        }

        $positions = $query->orderByDesc('PID')->get();
        $total     = Position::count();

        $stats = [
            'total'    => $total,
            'judicial' => Position::where('Pname', 'like', '%Judge%')->orWhere('Pname', 'like', '%Justice%')->count(),
            'admin'    => Position::where('Pname', 'like', '%IT%')->orWhere('Pname', 'like', '%Admin%')->orWhere('Pname', 'like', '%HR%')->count(),
            'registry' => Position::where('Pname', 'like', '%Registrar%')->orWhere('Pname', 'like', '%Clerk%')->count(),
        ];

        $nextCode = 'POS' . str_pad($total + 1, 3, '0', STR_PAD_LEFT);

        return view('setting.position_view', compact('positions', 'stats', 'nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Pcode' => 'required|unique:typeofposition,Pcode|max:50',
            'Pname' => 'required|max:100',
        ]);

        Position::create([
            'Pcode'       => strtoupper($request->input('Pcode')),
            'Pname'       => $request->input('Pname'),
            'addedBy'     => auth()->user()->name ?? 'Admin',
            'addedDate'   => now()->format('Y-m-d H:i:s'),
            'updatedBy'   => '',
            'updatedDate' => '',
        ]);

        return redirect()->route('position.index')->with('success', 'Position added successfully.');
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'Pname' => 'required|max:100',
        ]);

        $position->update([
            'Pname'       => $request->input('Pname'),
            'updatedBy'   => auth()->user()->name ?? 'Admin',
            'updatedDate' => now()->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('position.index')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('position.index')->with('success', 'Position deleted successfully.');
    }
}
