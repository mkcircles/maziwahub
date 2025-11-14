<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Group('Farm Management')]
class FarmerController extends Controller
{
    /**
     * Get Farmers
     * @description Get all farmers with their cows, milk deliveries and cow milk productions.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Farmer::class);

        $user = $request->user();
        $query = Farmer::query()->with(['cows', 'milkDeliveries.milkCollectionCenter', 'cowMilkProductions']);

        // Filter by role-based access
        if ($user->isPartner() && $user->partner_id) {
            $mccIds = $user->partner->milkCollectionCenters()->pluck('id')->toArray();
            $query->whereIn('milk_collection_center_id', $mccIds);
        } elseif ($user->isMcc() && $user->milk_collection_center_id) {
            $query->where('milk_collection_center_id', $user->milk_collection_center_id);
        } elseif ($user->agent) {
            $agent = $user->agent;
            if ($agent->milk_collection_center_id) {
                $query->where('milk_collection_center_id', $agent->milk_collection_center_id);
            } elseif ($agent->partner_id) {
                $mccIds = $agent->partner->milkCollectionCenters()->pluck('id')->toArray();
                $query->whereIn('milk_collection_center_id', $mccIds);
            }
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('farmer_id', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($insured = $request->query('is_farmer_insured')) {
            $query->where('is_farmer_insured', filter_var($insured, FILTER_VALIDATE_BOOL));
        }

        if ($regType = $request->query('reg_type')) {
            $query->where('reg_type', $regType);
        }

        return response()->json($query->orderBy('last_name')->orderBy('first_name')->paginate());
    }

    /**
     * Create Farmer
     * @description Create a new farmer.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Farmer::class);

        $data = $this->validatedData($request);
        $user = $request->user();

        $data['farmer_id'] = $data['farmer_id'] ?? $this->generateFarmerId();

        // Set MCC and agent based on user role
        if ($user->isMcc() && $user->milk_collection_center_id) {
            $data['milk_collection_center_id'] = $user->milk_collection_center_id;
        } elseif ($user->agent) {
            $agent = $user->agent;
            if ($agent->milk_collection_center_id) {
                $data['milk_collection_center_id'] = $agent->milk_collection_center_id;
            }
            $data['registered_by_agent_id'] = $agent->id;
        }

        $farmer = Farmer::create($data);

        return response()->json($farmer->load(['cows', 'milkDeliveries']), 201);
    }

    /**
     * Show Farmer
     * @description Get a farmer with their cows, milk deliveries and cow milk productions.
     */
    public function show(Farmer $farmer): JsonResponse
    {
        $this->authorize('view', $farmer);

        return response()->json($farmer->load(['cows', 'milkDeliveries.milkCollectionCenter', 'cowMilkProductions']));
    }

    /**
     * Update Farmer
     * @description Update a farmer.
     */
    public function update(Request $request, Farmer $farmer): JsonResponse
    {
        $this->authorize('update', $farmer);

        $data = $this->validatedData($request, $farmer);

        if (isset($data['farmer_id'])) {
            $data['farmer_id'] = Str::upper($data['farmer_id']);
        }

        $farmer->fill($data)->save();

        return response()->json($farmer->load(['cows', 'milkDeliveries.milkCollectionCenter']));
    }

    /**
     * Delete Farmer
     * @description Delete a farmer.
     */
    public function destroy(Farmer $farmer): JsonResponse
    {
        $this->authorize('delete', $farmer);

        $farmer->delete();

        return response()->json(null, 204);
    }

    protected function validatedData(Request $request, ?Farmer $farmer = null): array
    {
        $farmerId = $farmer?->id;

        $data = $request->validate([
            'farmer_id' => [
                'nullable',
                'string',
                'size:6',
                'regex:/^[A-Z0-9]{6}$/i',
                Rule::unique('farmers', 'farmer_id')->ignore($farmerId),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'educational_level' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'id_number' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'district' => ['nullable', 'string', 'max:255'],
            'county' => ['nullable', 'string', 'max:255'],
            'sub_county' => ['nullable', 'string', 'max:255'],
            'parish' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'next_of_kin' => ['nullable', 'string', 'max:255'],
            'next_of_kin_contact' => ['nullable', 'string', 'max:50'],
            'next_of_kin_relationship' => ['nullable', 'string', 'max:100'],
            'no_of_household_members' => ['nullable', 'integer', 'min:0'],
            'registered_by' => ['nullable', 'string', 'max:255'],
            'milk_collection_center_id' => ['nullable', 'integer', 'exists:milk_collection_centers,id'],
            'registered_by_agent_id' => ['nullable', 'integer', 'exists:agents,id'],
            'photo' => ['nullable', 'string', 'max:2048'],
            'coordinates' => ['nullable', 'array'],
            'coordinates.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'coordinates.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['nullable', 'string', 'max:50'],
            'validation_reason' => ['nullable', 'string'],
            'reg_type' => ['nullable', 'string', 'max:100'],
            'household_head' => ['nullable', 'boolean'],
            'family_size' => ['nullable', 'integer', 'min:0'],
            'male_members' => ['nullable', 'integer', 'min:0'],
            'female_members' => ['nullable', 'integer', 'min:0'],
            'above_18' => ['nullable', 'integer', 'min:0'],
            'below_5' => ['nullable', 'integer', 'min:0'],
            'financial_instrument' => ['nullable', 'string', 'max:255'],
            'available_energy_source' => ['nullable', 'string', 'max:255'],
            'farm_size' => ['nullable', 'numeric', 'min:0'],
            'land_under_use' => ['nullable', 'numeric', 'min:0'],
            'land_ownership' => ['nullable', 'string', 'max:255'],
            'farming_type' => ['nullable', 'string', 'max:255'],
            'crop_production' => ['nullable', 'string'],
            'animal_production' => ['nullable', 'string'],
            'is_farmer_insured' => ['nullable', 'boolean'],
            'support_needed' => ['nullable', 'string'],
        ]);

        if (! empty($data['coordinates'])) {
            $data['coordinates'] = [
                'latitude' => $data['coordinates']['latitude'] ?? null,
                'longitude' => $data['coordinates']['longitude'] ?? null,
            ];
        }

        if (isset($data['household_head'])) {
            $data['household_head'] = (bool) $data['household_head'];
        }

        if (isset($data['is_farmer_insured'])) {
            $data['is_farmer_insured'] = (bool) $data['is_farmer_insured'];
        }

        if (! empty($data['farmer_id'])) {
            $data['farmer_id'] = Str::upper($data['farmer_id']);
        }

        return $data;
    }

    protected function generateFarmerId(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $id = collect(range(1, 6))
                ->map(fn () => $characters[random_int(0, strlen($characters) - 1)])
                ->implode('');
        } while (Farmer::where('farmer_id', $id)->exists());

        return $id;
    }
}
