<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    /**
     * Display a listing of agents.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Agent::class);

        $user = $request->user();
        $query = Agent::query()->with(['user', 'milkCollectionCenter', 'partner']);

        // Filter by role-based access
        if ($user->isPartner() && $user->partner_id) {
            $query->where('partner_id', $user->partner_id);
        } elseif ($user->isMcc() && $user->milk_collection_center_id) {
            $query->where('milk_collection_center_id', $user->milk_collection_center_id);
        }

        // Search functionality
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by milk collection center
        if ($centerId = $request->query('milk_collection_center_id')) {
            $query->where('milk_collection_center_id', $centerId);
        }

        // Filter by partner
        if ($partnerId = $request->query('partner_id')) {
            $query->where('partner_id', $partnerId);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOL));
        }

        $query->orderBy('created_at', 'desc');

        if ($request->boolean('paginate') || $request->hasAny(['page', 'per_page'])) {
            return response()->json($query->paginate($request->integer('per_page') ?: 15));
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created agent.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Agent::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'milk_collection_center_id' => ['nullable', 'integer', 'exists:milk_collection_centers,id'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'is_active' => ['boolean'],
        ]);

        // Ensure at least one of MCC or Partner is provided
        if (empty($validated['milk_collection_center_id']) && empty($validated['partner_id'])) {
            return response()->json([
                'message' => 'Either milk_collection_center_id or partner_id must be provided.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create the user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'user_type' => 'agent',
                'milk_collection_center_id' => $validated['milk_collection_center_id'] ?? null,
                'partner_id' => $validated['partner_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Create the agent record
            $agent = Agent::create([
                'user_id' => $user->id,
                'milk_collection_center_id' => $validated['milk_collection_center_id'] ?? null,
                'partner_id' => $validated['partner_id'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return response()->json(
                $agent->load(['user', 'milkCollectionCenter', 'partner']),
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create agent.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified agent.
     */
    public function show(Agent $agent): JsonResponse
    {
        $this->authorize('view', $agent);

        return response()->json(
            $agent->load(['user', 'milkCollectionCenter', 'partner', 'registeredFarmers'])
        );
    }

    /**
     * Update the specified agent.
     */
    public function update(Request $request, Agent $agent): JsonResponse
    {
        $this->authorize('update', $agent);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($agent->user_id)],
            'password' => ['sometimes', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'milk_collection_center_id' => ['nullable', 'integer', 'exists:milk_collection_centers,id'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        DB::beginTransaction();
        try {
            // Update user if name, email, or password provided
            if (isset($validated['name']) || isset($validated['email']) || isset($validated['password'])) {
                $userUpdates = [];

                if (isset($validated['name'])) {
                    $userUpdates['name'] = $validated['name'];
                }

                if (isset($validated['email'])) {
                    $userUpdates['email'] = $validated['email'];
                }

                if (isset($validated['password'])) {
                    $userUpdates['password'] = Hash::make($validated['password']);
                }

                if (isset($validated['milk_collection_center_id'])) {
                    $userUpdates['milk_collection_center_id'] = $validated['milk_collection_center_id'];
                }

                if (isset($validated['partner_id'])) {
                    $userUpdates['partner_id'] = $validated['partner_id'];
                }

                if (isset($validated['is_active'])) {
                    $userUpdates['is_active'] = $validated['is_active'];
                }

                $agent->user->update($userUpdates);
            }

            // Update agent
            $agentUpdates = [];

            if (isset($validated['phone'])) {
                $agentUpdates['phone'] = $validated['phone'];
            }

            if (isset($validated['address'])) {
                $agentUpdates['address'] = $validated['address'];
            }

            if (isset($validated['milk_collection_center_id'])) {
                $agentUpdates['milk_collection_center_id'] = $validated['milk_collection_center_id'];
            }

            if (isset($validated['partner_id'])) {
                $agentUpdates['partner_id'] = $validated['partner_id'];
            }

            if (isset($validated['is_active'])) {
                $agentUpdates['is_active'] = $validated['is_active'];
            }

            $agent->update($agentUpdates);

            DB::commit();

            return response()->json(
                $agent->fresh(['user', 'milkCollectionCenter', 'partner'])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update agent.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deactivate the specified agent.
     */
    public function destroy(Agent $agent): JsonResponse
    {
        $this->authorize('delete', $agent);

        DB::beginTransaction();
        try {
            // Deactivate both agent and user instead of deleting
            $agent->update(['is_active' => false]);
            $agent->user->update(['is_active' => false]);

            DB::commit();

            return response()->json([
                'message' => 'Agent deactivated successfully.',
                'agent' => $agent->fresh(['user', 'milkCollectionCenter', 'partner']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to deactivate agent.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
