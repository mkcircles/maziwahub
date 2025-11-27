<?php

namespace App\Http\Controllers;

use App\Models\MilkCollectionCenterClaim;
use App\Models\Partner;
use App\Models\PartnerInvitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Partner::class);

        $partners = Partner::query()
            ->when($request->query('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%");
                });
            })
            ->withCount([
                'milkCollectionCenters',
                'milkCollectionCenterClaims as pending_claims_count' => fn ($q) => $q->where('status', MilkCollectionCenterClaim::STATUS_PENDING),
                'invitations as pending_invitations_count' => fn ($q) => $q->where('status', PartnerInvitation::STATUS_PENDING),
            ])
            ->orderBy('name')
            ->get();

        return response()->json($partners);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Partner::class);

        $data = $this->validatedData($request);

        $partner = Partner::create($data);

        return response()->json($partner->fresh(), 201);
    }

    public function show(Partner $partner): JsonResponse
    {
        $this->authorize('view', $partner);

        $partner->load([
            'milkCollectionCenters' => fn ($q) => $q
                ->with([
                    'pendingClaims' => fn ($pending) => $pending->latest(),
                ])
                ->withCount([
                    'farmers',
                    'pendingClaims',
                ])
                ->orderBy('name'),
            'invitations' => fn ($q) => $q->latest(),
        ]);

        return response()->json($partner);
    }

    public function update(Request $request, Partner $partner): JsonResponse
    {
        $this->authorize('update', $partner);

        $data = $this->validatedData($request, $partner->id);

        $partner->fill($data)->save();

        return response()->json($partner->fresh());
    }

    public function destroy(Partner $partner): JsonResponse
    {
        $this->authorize('delete', $partner);

        $partner->is_active = false;
        $partner->save();

        return response()->json(null, 204);
    }

    protected function validatedData(Request $request, ?int $partnerId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('partners', 'email')->ignore($partnerId),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'registration_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('partners', 'registration_number')->ignore($partnerId),
            ],
            'description' => ['nullable', 'string'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'contact_title' => ['nullable', 'string', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
