<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Support\SlugGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Region::query()->with(['country', 'districts.counties.subcounties.parishes']);

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->integer('country_id'));
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
                Rule::unique('regions', 'name')->where(fn ($query) => $query->where('country_id', $request->input('country_id'))),
            ],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'regions');

        $region = Region::create($validated)->load(['country', 'districts.counties.subcounties.parishes']);

        return response()->json($region, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Region $region)
    {
        return response()->json($region->load(['country', 'districts.counties.subcounties.parishes']));
    }

    /**
     * Update the specified resource in storage.
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

        return response()->json($region->load(['country', 'districts.counties.subcounties.parishes']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region)
    {
        $region->delete();

        return response()->json(null, 204);
    }

    public function districts(Region $region)
    {
        return response()->json(
            $region->districts()->with('counties.subcounties.parishes')->get()
        );
    }
}
