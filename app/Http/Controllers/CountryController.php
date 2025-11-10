<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Support\SlugGenerator;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Country::query()->with('regions.districts.counties.subcounties.parishes.villages')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->filled('iso_code')) {
            $request->merge(['iso_code' => strtoupper($request->input('iso_code'))]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:countries,name'],
            'iso_code' => ['required', 'string', 'size:3', 'unique:countries,iso_code'],
        ]);

        $validated['slug'] = SlugGenerator::generate($validated['name'], 'countries');

        $country = Country::create($validated)->load('regions.districts.counties.subcounties.parishes.villages');

        return response()->json($country, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        return response()->json($country->load('regions.districts.counties.subcounties.parishes.villages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        if ($request->filled('iso_code')) {
            $request->merge(['iso_code' => strtoupper($request->input('iso_code'))]);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:countries,name,' . $country->id],
            'iso_code' => ['sometimes', 'required', 'string', 'size:3', 'unique:countries,iso_code,' . $country->id],
        ]);

        if (array_key_exists('name', $validated)) {
            $validated['slug'] = SlugGenerator::generate($validated['name'], 'countries', $country->id);
        }

        $country->fill($validated)->save();

        return response()->json($country->load('regions.districts.counties.subcounties.parishes.villages'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json(null, 204);
    }

    public function regions(Country $country)
    {
        return response()->json(
            $country->regions()->with('districts.counties.subcounties.parishes.villages')->get()
        );
    }
}
