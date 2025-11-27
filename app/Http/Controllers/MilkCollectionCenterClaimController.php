<?php

namespace App\Http\Controllers;

use App\Models\MilkCollectionCenter;
use App\Models\MilkCollectionCenterClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MilkCollectionCenterClaimController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = MilkCollectionCenterClaim::query()
            ->with(['milkCollectionCenter', 'partner', 'requestedBy', 'respondedBy'])
            ->orderByDesc('created_at');

        if ($user->isAdminOrSuperAdmin()) {
            // admins can see all claims
        } else {
            $partnerId = $this->resolvePartnerId($user);
            abort_unless($partnerId, 403);

            $query->where('partner_id', $partnerId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json($query->get());
    }

    public function store(Request $request, MilkCollectionCenter $milkCollectionCenter): JsonResponse
    {
        $partnerId = $this->resolvePartnerId($request->user());
        abort_unless($partnerId, 403);

        $data = $request->validate([
            'message' => ['nullable', 'string'],
        ]);

        abort_if(
            $milkCollectionCenter->partner_id === $partnerId,
            422,
            'This milk collection center is already linked to your partner account.'
        );

        abort_if(
            $milkCollectionCenter->pendingClaims()
                ->where('partner_id', $partnerId)
                ->exists(),
            422,
            'A pending claim already exists for this milk collection center.'
        );

        $claim = MilkCollectionCenterClaim::create([
            'milk_collection_center_id' => $milkCollectionCenter->id,
            'partner_id' => $partnerId,
            'requested_by_user_id' => $request->user()->id,
            'message' => $data['message'] ?? null,
            'status' => MilkCollectionCenterClaim::STATUS_PENDING,
        ]);

        return response()->json($claim->fresh(['milkCollectionCenter', 'partner', 'requestedBy', 'respondedBy']), 201);
    }

    public function approve(Request $request, MilkCollectionCenterClaim $claim): JsonResponse
    {
        abort_unless($request->user()->isAdminOrSuperAdmin(), 403);

        $request->validate([
            'response_notes' => ['nullable', 'string'],
        ]);

        $claim->approve($request->user(), $request->input('response_notes'));

        $claim->milkCollectionCenter->update([
            'partner_id' => $claim->partner_id,
        ]);

        // Reject any other pending claims for the same MCC.
        $claim->milkCollectionCenter->claims()
            ->where('status', MilkCollectionCenterClaim::STATUS_PENDING)
            ->where('id', '!=', $claim->id)
            ->update([
                'status' => MilkCollectionCenterClaim::STATUS_REJECTED,
                'responded_by_user_id' => $request->user()->id,
                'responded_at' => now(),
                'response_notes' => 'Automatically rejected because another claim was approved.',
            ]);

        return response()->json($claim->fresh(['milkCollectionCenter', 'partner', 'requestedBy', 'respondedBy']));
    }

    public function reject(Request $request, MilkCollectionCenterClaim $claim): JsonResponse
    {
        abort_unless($request->user()->isAdminOrSuperAdmin(), 403);

        $data = $request->validate([
            'response_notes' => ['nullable', 'string'],
        ]);

        $claim->reject($request->user(), $data['response_notes'] ?? null);

        return response()->json($claim->fresh(['milkCollectionCenter', 'partner', 'requestedBy', 'respondedBy']));
    }

    protected function resolvePartnerId($user): ?int
    {
        if ($user->isPartner() && $user->partner_id) {
            return $user->partner_id;
        }

        if ($user->agent && $user->agent->partner_id) {
            return $user->agent->partner_id;
        }

        return null;
    }
}
