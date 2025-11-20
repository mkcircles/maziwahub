<?php

namespace App\Http\Controllers;

use App\Models\Parish;
use App\Support\SlugGenerator;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

#[Group('Geography')]
class ParishController extends Controller
{
    /**
     * Get Parishes
     * @description Get all parishes with their subcounties, counties, districts, regions and countries.
     */
    public function index(Request $request)
    {
        $query = Parish::query()->with(['subcounty.county.district.region.country', 'villages']);

        if ($request->filled('subcounty_id')) {
            $query->where('subcounty_id', $request->integer('subcounty_id'));
        }

        if ($request->filled('county_id')) {
            $query->whereHas('subcounty', fn ($subcountyQuery) => $subcountyQuery->where('county_id', $request->integer('county_id')));
        }

        if ($request->filled('district_id')) {
            $query->whereHas('subcounty.county', fn ($countyQuery) => $countyQuery->where('district_id', $request->integer('district_id')));
        }

        if ($request->filled('region_id')) {
            $query->whereHas('subcounty.county.district', fn ($districtQuery) => $districtQuery->where('region_id', $request->integer('region_id')));
        }

        if ($request->filled('country_id')) {
            $query->whereHas('subcounty.county.district.region', fn ($regionQuery) => $regionQuery->where('country_id', $request->integer('country_id')));
        }

        return response()->json($query->get());
    }

    /**
     * Create Parish
     * @description Create a new parish.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('parishes', 'parish_name')->where(fn ($query) => $query->where('subcounty_id', $request->input('subcounty_id'))),
            ],
            'subcounty_id' => ['required', 'integer', 'exists:subcounties,id'],
            'parish_code' => ['required', 'string', 'max:255', 'unique:parishes,parish_code'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'parishes');

        $parish = Parish::create($validated)->load(['subcounty.county.district.region.country', 'villages']);

        return response()->json($parish, 201);
    }

    /**
     * Show Parish
     * @description Get a parish with its subcounties, counties, districts, regions and countries.
     */
    public function show(Parish $parish)
    {
        return response()->json($parish->load(['subcounty.county.district.region.country', 'villages']));
    }

    /**
     * Update Parish
     * @description Update a parish.
     */
    public function update(Request $request, Parish $parish)
    {
        $subcountyId = $request->input('subcounty_id', $parish->subcounty_id);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('parishes', 'parish_name')->ignore($parish->id)->where(fn ($query) => $query->where('subcounty_id', $subcountyId)),
            ],
            'subcounty_id' => ['sometimes', 'required', 'integer', 'exists:subcounties,id'],
            'parish_code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('parishes', 'parish_code')->ignore($parish->id)],
        ]);

        if (array_key_exists('name', $validated)) {
            $validated['slug'] = SlugGenerator::generate($validated['name'], 'parishes', $parish->id);
        }

        $parish->fill($validated)->save();

        return response()->json($parish->load('subcounty.county.district.region.country'));
    }

    /**
     * Delete Parish
     * @description Delete a parish.
     */
    public function destroy(Parish $parish)
    {
        $parish->delete();

        return response()->json(null, 204);
    }

    /**
     * Get Villages by Parish
     * @description Get all villages by a parish.
     */
    public function villages(Parish $parish)
    {
        return response()->json(
            $parish->villages()->with('parish.subcounty.county.district.region.country')->get()
        );
    }
}
