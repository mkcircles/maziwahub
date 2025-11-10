<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Support\SlugGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VillageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Village::query()->with('parish.subcounty.county.district.region.country');

        if ($request->filled('parish_id')) {
            $query->where('parish_id', $request->integer('parish_id'));
        }

        if ($request->filled('subcounty_id')) {
            $query->whereHas('parish', fn ($parishQuery) => $parishQuery->where('subcounty_id', $request->integer('subcounty_id')));
        }

        if ($request->filled('county_id')) {
            $query->whereHas('parish.subcounty', fn ($subcountyQuery) => $subcountyQuery->where('county_id', $request->integer('county_id')));
        }

        if ($request->filled('district_id')) {
            $query->whereHas('parish.subcounty.county', fn ($countyQuery) => $countyQuery->where('district_id', $request->integer('district_id')));
        }

        if ($request->filled('region_id')) {
            $query->whereHas('parish.subcounty.county.district', fn ($districtQuery) => $districtQuery->where('region_id', $request->integer('region_id')));
        }

        if ($request->filled('country_id')) {
            $query->whereHas('parish.subcounty.county.district.region', fn ($regionQuery) => $regionQuery->where('country_id', $request->integer('country_id')));
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
                Rule::unique('villages', 'name')->where(fn ($query) => $query->where('parish_id', $request->input('parish_id'))),
            ],
            'parish_id' => ['required', 'integer', 'exists:parishes,id'],
            'village_code' => ['required', 'string', 'max:255', 'unique:villages,village_code'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'villages');

        $village = Village::create($validated)->load('parish.subcounty.county.district.region.country');

        return response()->json($village, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Village $village)
    {
        return response()->json($village->load('parish.subcounty.county.district.region.country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Village $village)
    {
        $parishId = $request->input('parish_id', $village->parish_id);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('villages', 'name')->ignore($village->id)->where(fn ($query) => $query->where('parish_id', $parishId)),
            ],
            'parish_id' => ['sometimes', 'required', 'integer', 'exists:parishes,id'],
            'village_code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('villages', 'village_code')->ignore($village->id)],
        ]);

        if (array_key_exists('name', $validated)) {
            $validated['slug'] = SlugGenerator::generate($validated['name'], 'villages', $village->id);
        }

        $village->fill($validated)->save();

        return response()->json($village->load('parish.subcounty.county.district.region.country'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Village $village)
    {
        $village->delete();

        return response()->json(null, 204);
    }
}
