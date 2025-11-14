<?php

namespace App\Http\Controllers;

use App\Models\Subcounty;
use App\Support\SlugGenerator;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

#[Group('Geography')]
class SubcountyController extends Controller
{
    /**
     * Get Subcounties
     * @description Get all subcounties with their counties, districts, regions and countries.
     */
    public function index(Request $request)
    {
        $query = Subcounty::query()->with(['county.district.region.country', 'parishes.villages']);

        if ($request->filled('county_id')) {
            $query->where('county_id', $request->integer('county_id'));
        }

        if ($request->filled('district_id')) {
            $query->whereHas('county', fn ($countyQuery) => $countyQuery->where('district_id', $request->integer('district_id')));
        }

        if ($request->filled('region_id')) {
            $query->whereHas('county.district', fn ($districtQuery) => $districtQuery->where('region_id', $request->integer('region_id')));
        }

        if ($request->filled('country_id')) {
            $query->whereHas('county.district.region', fn ($regionQuery) => $regionQuery->where('country_id', $request->integer('country_id')));
        }

        return response()->json($query->get());
    }

    /**
     * Create Subcounty
     * @description Create a new subcounty.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subcounties', 'name')->where(fn ($query) => $query->where('county_id', $request->input('county_id'))),
            ],
            'county_id' => ['required', 'integer', 'exists:counties,id'],
            'subcounty_code' => ['required', 'string', 'max:255', 'unique:subcounties,subcounty_code'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'subcounties');

        $subcounty = Subcounty::create($validated)->load(['county.district.region.country', 'parishes.villages']);

        return response()->json($subcounty, 201);
    }

    /**
     * Show Subcounty
     * @description Get a subcounty with its counties, districts, regions and countries.
     */
    public function show(Subcounty $subcounty)
    {
        return response()->json($subcounty->load(['county.district.region.country', 'parishes.villages']));
    }

    /**
     * Update Subcounty
     * @description Update a subcounty.
     */
    public function update(Request $request, Subcounty $subcounty)
    {
        $countyId = $request->input('county_id', $subcounty->county_id);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('subcounties', 'name')->ignore($subcounty->id)->where(fn ($query) => $query->where('county_id', $countyId)),
            ],
            'county_id' => ['sometimes', 'required', 'integer', 'exists:counties,id'],
            'subcounty_code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('subcounties', 'subcounty_code')->ignore($subcounty->id)],
        ]);

        if (array_key_exists('name', $validated)) {
            $validated['slug'] = SlugGenerator::generate($validated['name'], 'subcounties', $subcounty->id);
        }

        $subcounty->fill($validated)->save();

        return response()->json($subcounty->load(['county.district.region.country', 'parishes.villages']));
    }

    /**
     * Delete Subcounty
     * @description Delete a subcounty.
     */
    public function destroy(Subcounty $subcounty)
    {
        $subcounty->delete();

        return response()->json(null, 204);
    }

    /**
     * Get Parishes by Subcounty
     * @description Get all parishes by a subcounty.
     */
    public function parishes(Subcounty $subcounty)
    {
        return response()->json($subcounty->parishes()->get());
    }
}
