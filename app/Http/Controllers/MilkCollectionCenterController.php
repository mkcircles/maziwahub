<?php

namespace App\Http\Controllers;

use App\Models\MilkCollectionCenter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MilkCollectionCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MilkCollectionCenter::query();

        if ($search = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('registration_number', 'like', "%{$search}%"));
        }

        if ($request->filled('has_testing_equipment')) {
            $query->where('has_testing_equipment', filter_var($request->query('has_testing_equipment'), FILTER_VALIDATE_BOOL));
        }

        if ($request->filled('has_washing_bay')) {
            $query->where('has_washing_bay', filter_var($request->query('has_washing_bay'), FILTER_VALIDATE_BOOL));
        }

        return response()->json($query->orderBy('name')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $center = MilkCollectionCenter::create($validated);

        return response()->json($center, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MilkCollectionCenter $milkCollectionCenter)
    {
        return response()->json($milkCollectionCenter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MilkCollectionCenter $milkCollectionCenter)
    {
        $validated = $this->validateData($request, $milkCollectionCenter->id);

        $milkCollectionCenter->fill($validated)->save();

        return response()->json($milkCollectionCenter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MilkCollectionCenter $milkCollectionCenter)
    {
        $milkCollectionCenter->delete();

        return response()->json(null, 204);
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('milk_collection_centers', 'name')->ignore($id)],
            'registration_number' => ['nullable', 'string', 'max:255', Rule::unique('milk_collection_centers', 'registration_number')->ignore($id)],
            'physical_address' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location' => ['nullable', 'array'],
            'location.country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'location.region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'location.district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'location.county_id' => ['nullable', 'integer', 'exists:counties,id'],
            'location.subcounty_id' => ['nullable', 'integer', 'exists:subcounties,id'],
            'location.parish_id' => ['nullable', 'integer', 'exists:parishes,id'],
            'location.village_id' => ['nullable', 'integer', 'exists:villages,id'],
            'established_date' => ['nullable', 'date'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_phone' => ['nullable', 'string', 'max:50'],
            'staff_count' => ['nullable', 'integer', 'min:0'],
            'power_source' => ['nullable', 'string', 'max:255'],
            'cooler_capacity_liters' => ['nullable', 'integer', 'min:0'],
            'has_testing_equipment' => ['boolean'],
            'has_washing_bay' => ['boolean'],
        ]);
    }
}

