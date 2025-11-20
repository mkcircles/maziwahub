<?php

namespace App\Http\Controllers;

use App\Models\Vet;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParam;
use Dedoc\Scramble\Attributes\ResponseFrom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Vets')]
class VetController extends Controller
{
    
    /**
     * List Vets
     * @description List vets with optional filters.
     * @queryParam search Filter by name or license number.
     * @queryParam active_only Only return active vets.
     * @response 200 {
     *  "data": [
     *      {
     *          "id": 1,
     *          "first_name": "John",
     *          "last_name": "Doe",
     *          "license_number": "1234567890",
     *          "license_expiry_date": "2025-01-01",
     *          "phone_number": "1234567890",
     *          "email": "john.doe@example.com",
     *          "specialization": "Dairy",
     *          "employer": "Dairy Farm",
     *          "milk_collection_center_id": 1,
     *          "bio": "John Doe is a dairy vet with 10 years of experience.",
     *          "is_active": true,
     *          "created_at": "2025-01-01 12:00:00",
     *          "updated_at": "2025-01-01 12:00:00",
     *          "milkCollectionCenter": {
     *              "id": 1,
     *              "name": "Dairy Farm",
     *              "address": "1234567890",
     *              "phone": "1234567890",
     *              "email": "dairyfarm@example.com",
     *              "website": "https://dairyfarm.com",
     *              "created_at": "2025-01-01 12:00:00",
     *              "updated_at": "2025-01-01 12:00:00"
     *          }
     *      }
     *  ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vet::query()->with('milkCollectionCenter');

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search): void {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('last_name')->orderBy('first_name')->paginate());
    }

    /**
     * Create Vet
     * @description Create a new vet.
     * @bodyParam first_name string required The first name of the vet.
     * @bodyParam last_name string required The last name of the vet.
     * @bodyParam license_number string required The license number of the vet.
     * @bodyParam license_expiry_date date The expiry date of the vet's license.
     * @bodyParam phone_number string The phone number of the vet.
     * @bodyParam email string The email of the vet. 
     * @bodyParam specialization string The specialization of the vet.
     * @bodyParam employer string The employer of the vet.
     * @bodyParam milk_collection_center_id integer The milk collection center ID of the vet.
     * @bodyParam bio string The bio of the vet.
     * @bodyParam is_active boolean The active status of the vet.
     * @response 201 {
     *  "data": {
     *      "id": 1,
     *      "first_name": "John",
     *      "last_name": "Doe",
     *      "license_number": "1234567890",
     *      "license_expiry_date": "2025-01-01",
     *      "phone_number": "1234567890",
     *      "email": "john.doe@example.com",
     *      "specialization": "Dairy",
     *      "employer": "Dairy Farm",
     *      "milk_collection_center_id": 1,
     *      "bio": "John Doe is a dairy vet with 10 years of experience.",
     *      "is_active": true,
     *      "created_at": "2025-01-01 12:00:00",
     *      "updated_at": "2025-01-01 12:00:00",
     *      "milkCollectionCenter": {
     *          "id": 1,
     *          "name": "Dairy Farm",
     *          "address": "1234567890",
     *          "phone": "1234567890",
     *          "email": "dairyfarm@example.com",
     *          "website": "https://dairyfarm.com",
     *          "created_at": "2025-01-01 12:00:00",
     *          "updated_at": "2025-01-01 12:00:00"
     *      }
     *  }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $vet = Vet::create($data);

        return response()->json($vet->fresh('milkCollectionCenter'), 201);
    }


    /**
     * Get Vet
     * @description Get a specific vet.
     * @urlParam vet integer required The ID of the vet.
     * @response 200 {
     *  "data": {
     *      "id": 1,
     *      "first_name": "John",
     *      "last_name": "Doe",
     *      "license_number": "1234567890",
     *      "license_expiry_date": "2025-01-01",
     *      "phone_number": "1234567890",
     *      "email": "john.doe@example.com",
     *      "specialization": "Dairy",
     *      "employer": "Dairy Farm",
     *      "milk_collection_center_id": 1,
     *      "bio": "John Doe is a dairy vet with 10 years of experience.",
     *      "is_active": true,
     *      "created_at": "2025-01-01 12:00:00",
     *      "updated_at": "2025-01-01 12:00:00",
     *      "milkCollectionCenter": {
     *          "id": 1,
     *          "name": "Dairy Farm",
     *          "address": "1234567890",
     *          "phone": "1234567890",
     *          "email": "dairyfarm@example.com",
     *          "website": "https://dairyfarm.com",
     *          "created_at": "2025-01-01 12:00:00",
     *          "updated_at": "2025-01-01 12:00:00"
     *      }
     *  }
     * }
     */
    public function show(Vet $vet): JsonResponse
    {
        return response()->json($vet->load('milkCollectionCenter', 'treatments.cow'));
    }

    /**
     * Update Vet
     * @description Update an existing vet.
     * @urlParam vet integer required The ID of the vet.
     * @bodyParam first_name string The first name of the vet.
     * @bodyParam last_name string The last name of the vet.
     * @bodyParam license_number string The license number of the vet.
     * @bodyParam license_expiry_date date The expiry date of the vet's license.
     * @bodyParam phone_number string The phone number of the vet.
     * @bodyParam email string The email of the vet.
     * @bodyParam specialization string The specialization of the vet.
     * @bodyParam employer string The employer of the vet.
     * @bodyParam milk_collection_center_id integer The milk collection center ID of the vet.
     * @bodyParam bio string The bio of the vet.
     * @bodyParam is_active boolean The active status of the vet.
     * @response 200 {
     *  "data": {
     *      "id": 1,
     *      "first_name": "John",
     *      "last_name": "Doe",
     *      "license_number": "1234567890",
     *      "license_expiry_date": "2025-01-01",
     *      "phone_number": "1234567890",
     *      "email": "john.doe@example.com",
     *      "specialization": "Dairy",
     *      "employer": "Dairy Farm",
     *      "milk_collection_center_id": 1,
     *      "bio": "John Doe is a dairy vet with 10 years of experience.",
     *      "is_active": true,
     *      "created_at": "2025-01-01 12:00:00",
     *      "updated_at": "2025-01-01 12:00:00",
     *      "milkCollectionCenter": {
     *          "id": 1,
     *          "name": "Dairy Farm",
     *          "address": "1234567890",
     *          "phone": "1234567890",
     *          "email": "dairyfarm@example.com",
     *          "website": "https://dairyfarm.com",
     *          "created_at": "2025-01-01 12:00:00",
     *          "updated_at": "2025-01-01 12:00:00"
     *      }
     *  }
     * }
     */
    public function update(Request $request, Vet $vet): JsonResponse
    {
        $data = $this->validatedData($request, $vet->id);

        $vet->fill($data)->save();

        return response()->json($vet->fresh('milkCollectionCenter'));
    }

    /**
     * Delete Vet
     * @description Delete a vet.
     * @urlParam vet integer required The ID of the vet.
     * @response 204 {
     *  "message": "Vet deleted successfully"
     * }
     */
    public function destroy(Vet $vet): JsonResponse
    {
        $vet->delete();

        return response()->json(null, 204);
    }

    protected function validatedData(Request $request, ?int $vetId = null): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255', 'unique:vets,license_number,' . ($vetId ?? 'NULL')],
            'license_expiry_date' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'employer' => ['nullable', 'string', 'max:255'],
            'milk_collection_center_id' => ['nullable', 'exists:milk_collection_centers,id'],
            'bio' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
