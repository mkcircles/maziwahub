<?php

namespace App\Http\Controllers;

use App\Models\County;
use App\Support\SlugGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CountyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = County::query()->with(['district.region.country', 'subcounties.parishes.villages']);

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->integer('district_id'));
        }

        if ($request->filled('region_id')) {
            $query->whereHas('district', fn ($districtQuery) => $districtQuery->where('region_id', $request->integer('region_id')));
        }

        if ($request->filled('country_id')) {
            $query->whereHas('district.region', fn ($regionQuery) => $regionQuery->where('country_id', $request->integer('country_id')));
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
                Rule::unique('counties', 'name')->where(fn ($query) => $query->where('district_id', $request->input('district_id'))),
            ],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'county_code' => ['required', 'string', 'max:255', 'unique:counties,county_code'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'counties');

        $county = County::create($validated)->load(['district.region.country', 'subcounties.parishes.villages']);

        return response()->json($county, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(County $county)
    {
        return response()->json($county->load(['district.region.country', 'subcounties.parishes.villages']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, County $county)
    {
        $districtId = $request->input('district_id', $county->district_id);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('counties', 'name')->ignore($county->id)->where(fn ($query) => $query->where('district_id', $districtId)),
            ],
            'district_id' => ['sometimes', 'required', 'integer', 'exists:districts,id'],
            'county_code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('counties', 'county_code')->ignore($county->id)],
        ]);

        if (array_key_exists('name', $validated)) {
            $validated['slug'] = SlugGenerator::generate($validated['name'], 'counties', $county->id);
        }

        $county->fill($validated)->save();

        return response()->json($county->load(['district.region.country', 'subcounties.parishes.villages']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(County $county)
    {
        $county->delete();

        return response()->json(null, 204);
    }

    public function subcounties(County $county)
    {
        return response()->json(
            $county->subcounties()->with('parishes.villages')->get()
        );
    }
}
