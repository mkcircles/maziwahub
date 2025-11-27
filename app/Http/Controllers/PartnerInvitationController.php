<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Partner;
use App\Models\PartnerInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PartnerInvitationController extends Controller
{
    public function index(Partner $partner): JsonResponse
    {
        $this->authorize('view', $partner);

        $invitations = $partner->invitations()
            ->latest()
            ->get();

        return response()->json($invitations);
    }

    public function store(Request $request, Partner $partner): JsonResponse
    {
        $this->authorize('update', $partner);

        $data = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('partner_invitations', 'email')->where(function ($query) use ($partner) {
                    $query->where('partner_id', $partner->id)
                        ->where('status', PartnerInvitation::STATUS_PENDING);
                }),
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::in(['partner_admin', 'partner_agent'])],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'notes' => ['nullable', 'string'],
        ]);

        $invitation = $partner->invitations()->create([
            'email' => $data['email'],
            'name' => $data['name'] ?? null,
            'role' => $data['role'],
            'expires_at' => $data['expires_at'] ?? null,
            'notes' => $data['notes'] ?? null,
            'invited_by_user_id' => $request->user()?->id,
            'status' => PartnerInvitation::STATUS_PENDING,
        ]);

        // TODO: dispatch notification / email

        return response()->json($invitation->fresh(), 201);
    }

    public function destroy(Partner $partner, PartnerInvitation $invitation): JsonResponse
    {
        $this->authorize('update', $partner);

        abort_unless($invitation->partner_id === $partner->id, 404);

        $invitation->markRevoked();

        return response()->json($invitation->fresh());
    }

    public function accept(Request $request, string $token): JsonResponse
    {
        $invitation = PartnerInvitation::query()
            ->where('token', $token)
            ->firstOrFail();

        if ($invitation->status !== PartnerInvitation::STATUS_PENDING) {
            return response()->json([
                'message' => 'Invitation has already been processed.',
            ], 410);
        }

        if ($invitation->hasExpired()) {
            $invitation->status = PartnerInvitation::STATUS_EXPIRED;
            $invitation->save();

            return response()->json([
                'message' => 'Invitation has expired.',
            ], 410);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => Hash::make($data['password']),
            'user_type' => 'partner',
            'partner_id' => $invitation->partner_id,
            'is_active' => true,
        ]);

        if ($invitation->role === 'partner_agent') {
            Agent::create([
                'user_id' => $user->id,
                'partner_id' => $invitation->partner_id,
                'phone' => $data['phone'] ?? null,
                'is_active' => true,
            ]);
        } elseif ($data['phone'] ?? null) {
            $user->partner?->update([
                'phone' => $user->partner->phone ?? $data['phone'],
            ]);
        }

        $invitation->markAccepted();

        return response()->json([
            'message' => 'Invitation accepted successfully.',
            'user' => $user,
        ]);
    }
}
