<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\StateRegion;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::with('region')->orderBy('city_name');

        if ($request->filled('search')) {
            $query->where('city_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('region')) {
            $query->where('state_region_id', $request->region);
        }

        $perPage = $this->resolvePerPage($request);

        $cities = $query->paginate($perPage)->withQueryString();
        $totalCities = City::count();
        $regionsCovered = City::whereNotNull('state_region_id')->distinct()->count('state_region_id');
        $regions = StateRegion::orderBy('state_name')->get();

        return view('Courts.setting.city', compact('cities', 'totalCities', 'regionsCovered', 'regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|max:255|unique:cities,city_name,NULL,id,state_region_id,' . $request->state_region_id,
            'state_region_id' => 'required|exists:state_regions,id',
        ]);

        City::create($request->only('city_name', 'state_region_id'));

        return response()->json(['success' => true, 'message' => 'City created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'city_name' => 'required|string|max:255|unique:cities,city_name,' . $id . ',id,state_region_id,' . $request->state_region_id,
            'state_region_id' => 'required|exists:state_regions,id',
        ]);

        $city = City::findOrFail($id);
        $city->update($request->only('city_name', 'state_region_id'));

        return response()->json(['success' => true, 'message' => 'City updated successfully.']);
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json(['success' => true, 'message' => 'City deleted successfully.']);
    }
}
