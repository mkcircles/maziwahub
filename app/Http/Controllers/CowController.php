<?php

namespace App\Http\Controllers;

use App\Models\Cow;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

#[Group('Farm Management')]
class CowController extends Controller
{
    /**
     * Get Cows
     * @description Get all cows with their farmers.
     * @queryParam farmer_id Filter by farmer ID. Example: 1
     * @queryParam search Filter by tag number or breed. Example: 123456
     */
    public function index(Request $request): JsonResponse
    {
        $query = Cow::query()->with('farmer');

        if ($farmerId = $request->query('farmer_id')) {
            $query->where('farmer_id', $farmerId);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('tag_number', 'like', "%{$search}%")
                    ->orWhere('breed', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('tag_number')->get());
    }

    /**
     * Create Cow
     * @description Create a new cow.
     * 
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $cow = Cow::create($data);

        return response()->json($cow->load('farmer'), 201);
    }

    /**
     * Show Cow
     * @description Get a cow with their farmer and milk productions.
     */
    public function show(Cow $cow): JsonResponse
    {
        return response()->json($cow->load(['farmer', 'milkProductions']));
    }

    /**
     * Update Cow
     * @description Update a cow.
     */
    public function update(Request $request, Cow $cow): JsonResponse
    {
        $data = $this->validatedData($request, $cow);

        $cow->fill($data)->save();

        return response()->json($cow->load(['farmer', 'milkProductions']));
    }

    /**
     * Delete Cow
     * @description Delete a cow.
     */
    public function destroy(Cow $cow): JsonResponse
    {
        $cow->delete();

        return response()->json(null, 204);
    }

    /**
     * Validate Cow Data
     * @description Validate the data for a cow.
     */
    protected function validatedData(Request $request, ?Cow $cow = null): array
    {
        $cowId = $cow?->id;

        return $request->validate([
            'farmer_id' => ['required', 'integer', 'exists:farmers,id'],
            'tag_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cows', 'tag_number')->ignore($cowId),
            ],
            'breed' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'acquired_date' => ['nullable', 'date'],
            'milk_capacity_liters' => ['nullable', 'numeric', 'min:0'],
            'health_status' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
