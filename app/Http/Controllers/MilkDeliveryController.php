<?php

namespace App\Http\Controllers;

use App\Models\MilkDelivery;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Milk Operations')]
class MilkDeliveryController extends Controller
{
    /**
     * Get Milk Deliveries
     * @description Get all milk deliveries with their farmers and milk collection centers.
     *
     * @queryParam farmer_id Filter by farmer ID. Example: 1
     * @queryParam milk_collection_center_id Filter by milk collection center ID. Example: 1
     * @queryParam from Filter by delivery date from. Example: 2025-01-01
     * @queryParam to Filter by delivery date to. Example: 2025-12-31
     */
    public function index(Request $request): JsonResponse
    {
        $query = MilkDelivery::query()->with(['farmer', 'milkCollectionCenter']);

        if ($farmerId = $request->query('farmer_id')) {
            $query->where('farmer_id', $farmerId);
        }

        if ($centerId = $request->query('milk_collection_center_id')) {
            $query->where('milk_collection_center_id', $centerId);
        }

        if ($from = $request->query('from')) {
            $query->whereDate('delivery_date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->whereDate('delivery_date', '<=', $to);
        }

        return response()->json($query->orderByDesc('delivery_date')->get());
    }

    /**
     * Create Milk Delivery
     * @description Create a new milk delivery.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $data['total_amount'] = $this->calculateTotal($data);

        $delivery = MilkDelivery::create($data);

        return response()->json($delivery->load(['farmer', 'milkCollectionCenter']), 201);
    }

    /**
     * Show Milk Delivery
     * @description Get a milk delivery with their farmer and milk collection center.
     */

    public function show(MilkDelivery $milkDelivery): JsonResponse
    {
        return response()->json($milkDelivery->load(['farmer', 'milkCollectionCenter']));
    }

    /**
     * Update Milk Delivery
     * @description Update a milk delivery.
     */

    public function update(Request $request, MilkDelivery $milkDelivery): JsonResponse
    {
        $data = $this->validatedData($request, $milkDelivery);

        $data['total_amount'] = $this->calculateTotal($data, $milkDelivery);

        $milkDelivery->fill($data)->save();

        return response()->json($milkDelivery->load(['farmer', 'milkCollectionCenter']));
    }

    /**
     * Delete Milk Delivery
     * @description Delete a milk delivery.
     */

    public function destroy(MilkDelivery $milkDelivery): JsonResponse
    {
        $milkDelivery->delete();

        return response()->json(null, 204);
    }

    protected function validatedData(Request $request, ?MilkDelivery $delivery = null): array
    {
        return $request->validate([
            'farmer_id' => ['required', 'integer', 'exists:farmers,id'],
            'milk_collection_center_id' => ['required', 'integer', 'exists:milk_collection_centers,id'],
            'delivery_date' => ['required', 'date'],
            'volume_liters' => ['required', 'numeric', 'min:0'],
            'quality_grade' => ['nullable', 'string', 'max:50'],
            'fat_content' => ['nullable', 'numeric', 'min:0'],
            'price_per_liter' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'recorded_by' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    /**
     * Calculate Total
     * @description Calculate the total amount for a milk delivery.
     */
    protected function calculateTotal(array $data, ?MilkDelivery $delivery = null): ?float
    {
        if (array_key_exists('total_amount', $data) && $data['total_amount'] !== null) {
            return (float) $data['total_amount'];
        }

        $volume = $data['volume_liters'] ?? $delivery?->volume_liters;
        $price = $data['price_per_liter'] ?? $delivery?->price_per_liter;

        if ($volume !== null && $price !== null) {
            return round((float) $volume * (float) $price, 2);
        }

        return $delivery?->total_amount;
    }
}
