<?php

namespace App\Http\Controllers;

use App\Models\CowMilkProduction;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

#[Group('Milk Operations')]
class CowMilkProductionController extends Controller
{
    /**
     * Get Cow Milk Productions
     * @description Get all cow milk productions with their cows and farmers.
     * @queryParam cow_id Filter by cow ID. Example: 1
     * @queryParam farmer_id Filter by farmer ID. Example: 1
     * @queryParam from Filter by production date from. Example: 2025-01-01
     * @queryParam to Filter by production date to. Example: 2025-12-31
     */
    public function index(Request $request): JsonResponse
    {
        $query = CowMilkProduction::query()->with(['cow.farmer']);

        if ($cowId = $request->query('cow_id')) {
            $query->where('cow_id', $cowId);
        }

        if ($farmerId = $request->query('farmer_id')) {
            $query->whereHas('cow', fn ($q) => $q->where('farmer_id', $farmerId));
        }

        if ($from = $request->query('from')) {
            $query->whereDate('recorded_date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->whereDate('recorded_date', '<=', $to);
        }

        return response()->json($query->orderByDesc('recorded_date')->get());
    }

    /**
     * Create Cow Milk Production
     * @description Create a new cow milk production.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $data['total_volume_liters'] = $this->calculateTotal($data);

        $production = CowMilkProduction::create($data);

        return response()->json($production->load('cow.farmer'), 201);
    }

    /**
     * Show Cow Milk Production
     * @description Get a cow milk production with their cow and farmer.
     */
    public function show(CowMilkProduction $cowMilkProduction): JsonResponse
    {
        return response()->json($cowMilkProduction->load('cow.farmer'));
    }

    /**
     * Update Cow Milk Production
     * @description Update a cow milk production.
     */
    public function update(Request $request, CowMilkProduction $cowMilkProduction): JsonResponse
    {
        $data = $this->validatedData($request, $cowMilkProduction);

        $data['total_volume_liters'] = $this->calculateTotal($data, $cowMilkProduction);

        $cowMilkProduction->fill($data)->save();

        return response()->json($cowMilkProduction->load('cow.farmer'));
    }

    /**
     * Delete Cow Milk Production
     * @description Delete a cow milk production.
     */
    public function destroy(CowMilkProduction $cowMilkProduction): JsonResponse
    {
        $cowMilkProduction->delete();

        return response()->json(null, 204);
    }

    /**
     * Validate Cow Milk Production Data
     * @description Validate the data for a cow milk production.
     */
    protected function validatedData(Request $request, ?CowMilkProduction $production = null): array
    {
        $data = $request->validate([
            'cow_id' => ['required', 'integer', 'exists:cows,id'],
            'recorded_date' => ['required', 'date'],
            'morning_volume_liters' => ['nullable', 'numeric', 'min:0'],
            'midday_volume_liters' => ['nullable', 'numeric', 'min:0'],
            'evening_volume_liters' => ['nullable', 'numeric', 'min:0'],
            'total_volume_liters' => ['nullable', 'numeric', 'min:0'],
            'recorded_by' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $exists = CowMilkProduction::query()
            ->where('cow_id', $data['cow_id'])
            ->whereDate('recorded_date', $data['recorded_date'])
            ->when($production, fn ($query) => $query->whereKeyNot($production->id))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'recorded_date' => ['This cow already has a record for the selected date.'],
            ]);
        }

        return $data;
    }

    /**
     * Calculate Total
     * @description Calculate the total volume for a cow milk production.
     */
    protected function calculateTotal(array $data, ?CowMilkProduction $production = null): ?float
    {
        if (array_key_exists('total_volume_liters', $data) && $data['total_volume_liters'] !== null) {
            return (float) $data['total_volume_liters'];
        }

        $volumes = collect([
            $data['morning_volume_liters'] ?? $production?->morning_volume_liters,
            $data['midday_volume_liters'] ?? $production?->midday_volume_liters,
            $data['evening_volume_liters'] ?? $production?->evening_volume_liters,
        ])->filter(fn ($value) => $value !== null);

        if ($volumes->isEmpty()) {
            return $production?->total_volume_liters;
        }

        return round($volumes->sum(), 2);
    }
}
