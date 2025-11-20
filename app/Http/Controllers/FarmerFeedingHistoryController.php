<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\FarmerFeedingHistory;
use App\Models\FeedingMethod;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\SubGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

#[Group('Farm Management')]
#[SubGroup('Feeding')]
class FarmerFeedingHistoryController extends Controller
{
    /**
     * List Feeding History
     * @description Get feeding history entries for a farmer.
     */
    public function index(Request $request, Farmer $farmer): JsonResponse
    {
        $this->authorize('view', $farmer);

        $type = $request->query('feeding_type');

        $query = $farmer->feedingHistories()->with(['feedingMethod', 'recordedBy'])->orderByDesc('started_at');

        if ($type) {
            $query->where('feeding_type', $type);
        }

        return response()->json($query->paginate($request->integer('per_page', 25)));
    }

    /**
     * Create Feeding History Entry
     * @description Record a change in a farmer's feeding practice.
     */
    public function store(Request $request, Farmer $farmer): JsonResponse
    {
        $this->authorize('update', $farmer);

        $data = $this->validatedData($request);

        $startedAt = isset($data['started_at'])
            ? Carbon::parse($data['started_at'])
            : now();

        $this->closeExistingOpenEntry($farmer, $data['feeding_type'], $startedAt);

        $history = new FarmerFeedingHistory([
            'feeding_method_id' => $data['feeding_method_id'] ?? null,
            'feeding_type' => $data['feeding_type'],
            'started_at' => $startedAt,
            'ended_at' => $data['ended_at'] ?? null,
            'notes' => $data['notes'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'recorded_by_id' => $request->user()?->id,
        ]);

        $farmer->feedingHistories()->save($history);

        $this->applyFarmerSnapshotUpdates($farmer, $data, $history);

        return response()->json($history->load(['feedingMethod', 'recordedBy', 'farmer']), 201);
    }

    /**
     * Update Feeding History Entry
     * @description Update an existing feeding history record.
     */
    public function update(Request $request, Farmer $farmer, FarmerFeedingHistory $feedingHistory): JsonResponse
    {
        $this->authorize('update', $farmer);
        abort_if($feedingHistory->farmer_id !== $farmer->id, 404);

        $data = $this->validatedData($request, $feedingHistory);

        if (isset($data['started_at'])) {
            $data['started_at'] = Carbon::parse($data['started_at']);
        }

        if (isset($data['ended_at'])) {
            $data['ended_at'] = Carbon::parse($data['ended_at']);
        }

        $feedingHistory->fill($data)->save();

        if ($feedingHistory->ended_at && $farmer->feedingHistories()
            ->where('feeding_type', $feedingHistory->feeding_type)
            ->whereNull('ended_at')
            ->where('id', '!=', $feedingHistory->id)
            ->doesntExist()
        ) {
            $this->refreshFarmerSnapshot($farmer, $feedingHistory->feeding_type);
        } elseif (
            in_array($feedingHistory->feeding_type, ['primary', 'supplemental'], true)
            && isset($data['feeding_method_id'])
        ) {
            $this->refreshFarmerSnapshot($farmer, $feedingHistory->feeding_type);
        }

        return response()->json($feedingHistory->load(['feedingMethod', 'recordedBy', 'farmer']));
    }

    /**
     * Delete Feeding History Entry
     * @description Delete a feeding history entry.
     */
    public function destroy(Farmer $farmer, FarmerFeedingHistory $feedingHistory): JsonResponse
    {
        $this->authorize('update', $farmer);
        abort_if($feedingHistory->farmer_id !== $farmer->id, 404);

        $type = $feedingHistory->feeding_type;
        $wasActive = $feedingHistory->ended_at === null;

        $feedingHistory->delete();

        if ($wasActive) {
            $this->refreshFarmerSnapshot($farmer, $type);
        }

        return response()->json(null, 204);
    }

    protected function validatedData(Request $request, ?FarmerFeedingHistory $history = null): array
    {
        $data = $request->validate([
            'feeding_method_id' => ['nullable', 'integer', 'exists:feeding_methods,id'],
            'feeding_type' => ['required', Rule::in(['primary', 'supplemental', 'other'])],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'feeding_notes' => ['nullable', 'string'],
            'feeding_metadata' => ['nullable', 'array'],
        ]);

        if ($data['feeding_type'] !== 'other' && empty($data['feeding_method_id'])) {
            abort(422, 'A feeding method is required for primary and supplemental feeding types.');
        }

        if ($history && $history->feeding_type !== $data['feeding_type']) {
            abort(422, 'The feeding type of an existing history entry cannot be changed.');
        }

        return $data;
    }

    protected function closeExistingOpenEntry(Farmer $farmer, string $type, Carbon $startedAt): void
    {
        $farmer->feedingHistories()
            ->where('feeding_type', $type)
            ->whereNull('ended_at')
            ->update(['ended_at' => $startedAt]);
    }

    protected function applyFarmerSnapshotUpdates(Farmer $farmer, array $data, FarmerFeedingHistory $history): void
    {
        $dirty = false;

        if ($data['feeding_type'] === 'primary') {
            $farmer->primary_feeding_method_id = $history->feeding_method_id;
            $dirty = true;
        } elseif ($data['feeding_type'] === 'supplemental') {
            $farmer->supplemental_feeding_method_id = $history->feeding_method_id;
            $dirty = true;
        }

        if (array_key_exists('feeding_metadata', $data)) {
            $farmer->feeding_metadata = $data['feeding_metadata'];
            $dirty = true;
        }

        if (array_key_exists('feeding_notes', $data)) {
            $farmer->feeding_notes = $data['feeding_notes'];
            $dirty = true;
        } elseif (! empty($data['notes']) && empty($farmer->feeding_notes)) {
            $farmer->feeding_notes = $data['notes'];
            $dirty = true;
        }

        if ($dirty) {
            $farmer->feeding_last_changed_at = now();
            $farmer->save();
        }
    }

    protected function refreshFarmerSnapshot(Farmer $farmer, string $type): void
    {
        $latest = $farmer->feedingHistories()
            ->where('feeding_type', $type)
            ->orderByDesc('started_at')
            ->orderByDesc('created_at')
            ->first();

        if ($type === 'primary') {
            $farmer->primary_feeding_method_id = $latest?->feeding_method_id;
        } elseif ($type === 'supplemental') {
            $farmer->supplemental_feeding_method_id = $latest?->feeding_method_id;
        }

        if ($latest) {
            $farmer->feeding_last_changed_at = now();
        }

        $farmer->save();
    }
}


