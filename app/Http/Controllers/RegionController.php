<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Support\SlugGenerator;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

#[Group('Geography')]
class RegionController extends Controller
{
    /**
     * Get Regions
     * @description Get all regions with their countries, districts, counties, subcounties, parishes and villages.
     */
    public function index(Request $request)
    {
        $query = Region::query()->with(['country']);

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->integer('country_id'));
        }

        return response()->json($query->get());
    }

    /**
     * Create Region
     * @description Create a new region.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions', 'name')->where(fn ($query) => $query->where('country_id', $request->input('country_id'))),
            ],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'regions');

        $region = Region::create($validated)->load(['country', 'districts.counties.subcounties.parishes.villages']);

        return response()->json($region, 201);
    }

    /**
     * Show Region
     * @description Get a region with its countries, districts, counties, subcounties, parishes and villages.
     */
    public function show(Region $region)
    {
        return response()->json($region->load(['country', 'districts.counties.subcounties.parishes.villages']));
    }

    /**
     * Update Region
     * @description Update a region.
     */
    public function update(Request $request, Region $region)
    {
        $countryId = $request->input('country_id', $region->country_id);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('regions', 'name')->ignore($region->id)->where(fn ($query) => $query->where('country_id', $countryId)),
            ],
            'country_id' => ['sometimes', 'required', 'integer', 'exists:countries,id'],
        ]);

        if (array_key_exists('name', $validated)) {
            $validated['slug'] = SlugGenerator::generate($validated['name'], 'regions', $region->id);
        }

        $region->fill($validated)->save();

        return response()->json($region->load(['country', 'districts.counties.subcounties.parishes.villages']));
    }

    /**
     * Delete Region
     * @description Delete a region.
     */
    public function destroy(Region $region)
    {
        $region->delete();

        return response()->json(null, 204);
    }

    /**
     * Get Districts by Region
     * @description Get all districts with their regions, countries and counties.
     */
    public function districts(Region $region)
    {
        return response()->json($region->districts()->get());
    }
}
