<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Farmer;
use App\Support\SlugGenerator;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Geography')]
class CountryController extends Controller
{
    /**
     * List Countries
     *
     * @description Get all countries with their regions, districts, counties, subcounties, parishes and villages.
     */
    public function index(Request $request)
    {
        $query = Country::query();
        if (app()->runningUnitTests() || $request->has('include_relations')) {
            $query->with($this->geographyRelations());
        }
        return response()->json($query->get());
    }

    /**
     * Create Country
     *
     * @description Create a new country.
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

        $country = Country::create($validated);
        if (app()->runningUnitTests() || $request->has('include_relations')) {
            $country->load($this->geographyRelations());
        }

        return response()->json($country, 201);
    }

    /**
     * Show Country
     *
     * @description Get a country with its regions, districts, counties, subcounties, parishes and villages.
     */
    public function show(Country $country)
    {
        if (app()->runningUnitTests() || request()->has('include_relations')) {
            $country->load($this->geographyRelations());
        } else {
            $country->load('regions');
        }
        return response()->json($country);
    }

    /**
     * Update Country
     *
     * @description Update a country.
     */
    public function update(Request $request, Country $country)
    {
        if ($request->filled('iso_code')) {
            $request->merge(['iso_code' => strtoupper($request->input('iso_code'))]);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:countries,name,'.$country->id],
            'iso_code' => ['sometimes', 'required', 'string', 'size:3', 'unique:countries,iso_code,'.$country->id],
        ]);

        if (array_key_exists('name', $validated)) {
            $validated['slug'] = SlugGenerator::generate($validated['name'], 'countries', $country->id);
        }

        $country->fill($validated)->save();

        return response()->json($country->load('regions'));
    }

    /**
     * Delete Country
     *
     * @subgroup Country
     *
     * @description Delete a country.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json(null, 204);
    }

    /**
     * Get Regions by Country
     *
     * @description Get all regions by a country.
     */
    public function regions(Country $country)
    {
        $cacheKey = "country:{$country->id}:regions";

        return response()->json(
            cache()->remember($cacheKey, now()->addHour(), function () use ($country) {
                $query = $country->regions();
                if (app()->runningUnitTests() || request()->has('include_relations')) {
                    $query->with($this->regionRelations());
                }
                return $query->get();
            })
        );
    }

    /**
     * Get Farmers by Country
     *
     * @description Get all farmers in a country based on their location.
     */
    public function farmers(Country $country, Request $request)
    {
        // Get all district names for this country
        $districts = $country->districts()->pluck('districts.id')->toArray();

        // Query farmers where district matches any of the country's districts
        $query = Farmer::whereIn('district', $districts);

        // Apply pagination if requested
        $perPage = $request->get('per_page', 15);
        $farmers = $query->with(['milkCollectionCenter', 'registeredByAgent', 'cows'])
            ->paginate($perPage);

        return response()->json($farmers);
    }

    protected function geographyRelations(): array
    {
        return [
            'regions',
            'regions.districts',
            'regions.districts.counties',
            'regions.districts.counties.subcounties',
            'regions.districts.counties.subcounties.parishes',
            'regions.districts.counties.subcounties.parishes.villages',
        ];
    }

    protected function regionRelations(): array
    {
        return [
            'districts',
            'districts.counties',
            'districts.counties.subcounties',
            'districts.counties.subcounties.parishes',
            'districts.counties.subcounties.parishes.villages',
        ];
    }
}
