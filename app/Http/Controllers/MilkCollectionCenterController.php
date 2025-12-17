<?php

namespace App\Http\Controllers;

use App\Models\MilkCollectionCenter;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

#[Group('Milk Collection')]
class MilkCollectionCenterController extends Controller
{
    /**
     * Get Milk Collection Centers
     * @description Get all milk collection centers.
     */
    public function index(Request $request)
    {
        $query = MilkCollectionCenter::query();

        if ($search = $request->query('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('registration_number', 'like', "%{$search}%"));
        }

        if ($request->filled('has_testing_equipment')) {
            $query->where('has_testing_equipment', filter_var($request->query('has_testing_equipment'), FILTER_VALIDATE_BOOL));
        }

        if ($request->filled('has_washing_bay')) {
            $query->where('has_washing_bay', filter_var($request->query('has_washing_bay'), FILTER_VALIDATE_BOOL));
        }

        foreach (['country_id', 'region_id', 'district_id', 'county_id', 'subcounty_id', 'parish_id', 'village_id'] as $key) {
            if ($request->filled($key)) {
                $value = (int) $request->query($key);
                $query->whereJsonContains("location->$key", $value);
            }
        }

        return response()->json($query->orderBy('name')->get());
    }

    /**
     * Create Milk Collection Center
     * @description Create a new milk collection center.
     */
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $validated['partner_id'] = $this->resolvePartnerIdForRequest($request, $validated['partner_id'] ?? null);

        $center = MilkCollectionCenter::create($validated);

        return response()->json($center, 201);
    }

    /**
     * Show Milk Collection Center
     * @description Get a milk collection center.
     */
    public function show(MilkCollectionCenter $milkCollectionCenter)
    {
        return response()->json($milkCollectionCenter);
    }

    /**
     * Update Milk Collection Center
     * @description Update a milk collection center.
     */
    public function update(Request $request, MilkCollectionCenter $milkCollectionCenter)
    {
        $validated = $this->validateData($request, $milkCollectionCenter->id);

        $validated['partner_id'] = $this->resolvePartnerIdForRequest($request, $validated['partner_id'] ?? $milkCollectionCenter->partner_id);

        $milkCollectionCenter->fill($validated)->save();

        return response()->json($milkCollectionCenter);
    }

    /**
     * Get Deliveries for Center
     * @description Get all milk deliveries for a specific milk collection center.
     */
    public function deliveries(Request $request, MilkCollectionCenter $milkCollectionCenter)
    {
        $query = $milkCollectionCenter->milkDeliveries()
            ->with(['farmer'])
            ->latest('delivery_date');

        if ($request->has('limit')) {
            $limit = (int) $request->query('limit');
            return response()->json($query->limit($limit)->get());
        }

        return response()->json($query->get());
    }

    /**
     * Delete Milk Collection Center
     * @description Delete a milk collection center.
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
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
        ]);
    }

    protected function resolvePartnerIdForRequest(Request $request, ?int $incomingPartnerId = null): ?int
    {
        $user = $request->user();

        if ($user->isAdminOrSuperAdmin()) {
            return $incomingPartnerId;
        }

        if ($user->isPartner() && $user->partner_id) {
            return $user->partner_id;
        }

        if ($user->agent && $user->agent->partner_id) {
            return $user->agent->partner_id;
        }

        return $incomingPartnerId;
    }
}

