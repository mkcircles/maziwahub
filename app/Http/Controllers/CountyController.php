<?php

namespace App\Http\Controllers;

use App\Models\County;
use App\Support\SlugGenerator;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

#[Group('Geography')]
class CountyController extends Controller
{
    /**
     * Get Counties
     * @description Get all counties with their districts, regions and countries.
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
     * Create County
     * @description Create a new county.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('counties', 'county_name')->where(fn ($query) => $query->where('district_id', $request->input('district_id'))),
            ],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'county_code' => ['required', 'string', 'max:255', 'unique:counties,county_code'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'counties');

        $county = County::create($validated)->load(['district.region.country', 'subcounties.parishes.villages']);

        return response()->json($county, 201);
    }

    /**
     * Show County
     * @description Get a county with its districts, regions and countries.
     * Display the specified resource.
     */
    public function show(County $county)
    {
        return response()->json($county->load(['district.region.country', 'subcounties.parishes.villages']));
    }

    /**
     * Update County
     * @description Update a county.
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
                Rule::unique('counties', 'county_name')->ignore($county->id)->where(fn ($query) => $query->where('district_id', $districtId)),
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
     * Delete County
     * @description Delete a county.
     */
    public function destroy(County $county)
    {
        $county->delete();

        return response()->json(null, 204);
    }

    /**
     * Get Subcounties by County
     * @description Get all subcounties by a county.
     */
    public function subcounties(County $county)
    {
        return response()->json(
            $county->subcounties()->with('parishes.villages')->get()
        );
    }
}
