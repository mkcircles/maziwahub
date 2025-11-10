<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Support\SlugGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = District::query()->with(['region.country', 'counties.subcounties.parishes.villages']);

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->integer('region_id'));
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('districts', 'name')->where(fn ($query) => $query->where('region_id', $request->input('region_id'))),
            ],
            'district_code' => ['required', 'string', 'max:255', 'unique:districts,district_code'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'districts');

        $district = District::create($validated)->load(['region.country', 'counties.subcounties.parishes.villages']);

        return response()->json($district, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        return response()->json($district->load(['region.country', 'counties.subcounties.parishes.villages']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, District $district)
    {
        $regionId = $request->input('region_id', $district->region_id);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('districts', 'name')->ignore($district->id)->where(fn ($query) => $query->where('region_id', $regionId)),
            ],
            'district_code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('districts', 'district_code')->ignore($district->id)],
            'region_id' => ['sometimes', 'required', 'integer', 'exists:regions,id'],
        ]);

        if (array_key_exists('name', $validated)) {
            $validated['slug'] = SlugGenerator::generate($validated['name'], 'districts', $district->id);
        }

        $district->fill($validated)->save();

        return response()->json($district->load(['region.country', 'counties.subcounties.parishes.villages']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        $district->delete();

        return response()->json(null, 204);
    }

    public function counties(District $district)
    {
        return response()->json(
            $district->counties()->with('subcounties.parishes.villages')->get()
        );
    }
}
