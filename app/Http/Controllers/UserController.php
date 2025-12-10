<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (super_admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->with(['milkCollectionCenter', 'partner', 'agent']);

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->input('user_type'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOL));
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('name')->paginate());
    }

    /**
     * Store a newly created user (super_admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'user_type' => ['required', 'string', Rule::in(['super_admin', 'admin', 'partner', 'mcc', 'user', 'agent'])],
            'milk_collection_center_id' => ['nullable', 'integer', 'exists:milk_collection_centers,id'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'is_active' => ['boolean'],
        ]);

        // Only super_admin can create super_admin and admin
        if (in_array($validated['user_type'], ['super_admin', 'admin'])) {
            if (!$request->user()->isSuperAdmin()) {
                return response()->json(['message' => 'Only super admins can create super admins and admins.'], 403);
            }
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $user = User::create($validated);

        return response()->json($user->load(['milkCollectionCenter', 'partner', 'agent']), 201);
    }

    /**
     * Display the specified user
     */
    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json($user->load(['milkCollectionCenter', 'partner', 'agent']));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'string', 'min:8'],
            'user_type' => ['sometimes', 'required', 'string', Rule::in(['super_admin', 'admin', 'partner', 'mcc', 'user'])],
            'milk_collection_center_id' => ['nullable', 'integer', 'exists:milk_collection_centers,id'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Only super_admin can change user_type to super_admin or admin
        if (isset($validated['user_type']) && in_array($validated['user_type'], ['super_admin', 'admin'])) {
            if (!$request->user()->isSuperAdmin()) {
                return response()->json(['message' => 'Only super admins can assign super admin or admin roles.'], 403);
            }
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->fill($validated)->save();

        return response()->json($user->load(['milkCollectionCenter', 'partner', 'agent']));
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(Request $request, User $user): JsonResponse
    {
        $this->authorize('toggleActive', $user);

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.',
            'user' => $user->load(['milkCollectionCenter', 'partner', 'agent']),
        ]);
    }

    /**
     * Remove the specified user (not allowed - returns 403)
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        // Users cannot be deleted, only deactivated
        return response()->json(['message' => 'Users cannot be deleted. Please deactivate the user instead.'], 403);
    }
}
