<?php

namespace App\Http\Controllers;

use App\Models\FeedingMethod;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\SubGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Group('Farm Management')]
#[SubGroup('Feeding')]
class FeedingMethodController extends Controller
{
    /**
     * List Feeding Methods
     * @description Get all feeding methods available in the platform.
     */
    public function index(): JsonResponse
    {
        $methods = FeedingMethod::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return response()->json($methods);
    }

    /**
     * Create Feeding Method
     * @description Create a new feeding method definition.
     */
    public function store(Request $request): JsonResponse
    {
        $this->ensureCanManage($request);

        $data = $this->validatedData($request);

        if (empty($data['code'])) {
            $data['code'] = $this->generateCode($data['name']);
        }

        $method = FeedingMethod::create($data);

        return response()->json($method, 201);
    }

    /**
     * Show Feeding Method
     * @description Retrieve a single feeding method.
     */
    public function show(FeedingMethod $feedingMethod): JsonResponse
    {
        return response()->json($feedingMethod);
    }

    /**
     * Update Feeding Method
     * @description Update a feeding method definition.
     */
    public function update(Request $request, FeedingMethod $feedingMethod): JsonResponse
    {
        $this->ensureCanManage($request);

        $data = $this->validatedData($request, $feedingMethod);

        if (array_key_exists('code', $data) && empty($data['code'])) {
            $data['code'] = $this->generateCode($data['name'] ?? $feedingMethod->name);
        }

        $feedingMethod->update($data);

        return response()->json($feedingMethod->fresh());
    }

    /**
     * Delete Feeding Method
     * @description Remove a feeding method. Methods linked to farmers cannot be deleted.
     */
    public function destroy(Request $request, FeedingMethod $feedingMethod): JsonResponse
    {
        $this->ensureCanManage($request);

        abort_if(
            $feedingMethod->primaryFarmers()->exists() || $feedingMethod->supplementalFarmers()->exists(),
            422,
            'Feeding method is in use by at least one farmer.'
        );

        $feedingMethod->delete();

        return response()->json(null, 204);
    }

    protected function ensureCanManage(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->isAdmin()), 403, 'You are not permitted to manage feeding methods.');
    }

    protected function validatedData(Request $request, ?FeedingMethod $feedingMethod = null): array
    {
        $id = $feedingMethod?->id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('feeding_methods', 'name')->ignore($id)],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('feeding_methods', 'code')->ignore($id)],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'requires_details' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (array_key_exists('requires_details', $data)) {
            $data['requires_details'] = (bool) $data['requires_details'];
        }

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        return $data;
    }

    protected function generateCode(string $name): string
    {
        $base = Str::upper(Str::slug($name, '_'));
        $candidate = $base;
        $suffix = 1;

        while (FeedingMethod::query()->where('code', $candidate)->exists()) {
            $candidate = sprintf('%s_%d', $base, $suffix++);
        }

        return $candidate;
    }
}


