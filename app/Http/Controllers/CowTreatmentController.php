<?php

namespace App\Http\Controllers;

use App\Models\CowTreatment;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\SubGroup;
use Dedoc\Scramble\Attributes\QueryParam;
use Dedoc\Scramble\Attributes\ResponseFrom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

#[Group('Farm Management')]
#[SubGroup('Cow Treatments')]
class CowTreatmentController extends Controller
{
    /**
     * List Cow Treatments
     * @description List cow treatments with optional filters.
     * @queryParam cow_id Filter by cow ID. Example: 1
     * @queryParam vet_id Filter by vet ID. Example: 1
     * @queryParam status Filter by status. Example: active
     * @queryParam timeline Return all treatments ordered descending without pagination. Example: true
     * @response 200 {
     *  "data": [
     *      {
     *          "id": 1,
     *          "cow_id": 1,
     *          "vet_id": 1,
     *          "status": "active",
     *          "treatment_date": "2025-01-01",
     *          "diagnosis": "Fever",
     *          "reason": "The cow is sick.",
     *          "medication": "Amoxicillin",
     *          "dosage": "100mg",
     *          "dosage_unit": "mg",
     *          "route": "Oral",
     *          "follow_up_date": "2025-01-02",
     *          "status": "active",
     *          "outcome": "Recovered",
     *          "next_steps": "Monitor the cow for 2 weeks.",
     *          "cost": 100000,
     *          "notes": "The cow is recovering well.",
     *          "attachment_path": "https://example.com/attachment.pdf",
     *          "life_cycle_status": "Healthy",
     *          "cow": {
     *              "id": 1,
     *              "tag_number": "1234567890",
     *              "breed": "Holstein",
     *              "date_of_birth": "2020-01-01",
     *              "acquired_date": "2021-01-01",
     *              "health_status": "Healthy",
     *              "milk_capacity_liters": 10000,
     *              "notes": "The cow is healthy and producing milk.",
     *              "created_at": "2025-01-01 12:00:00",
     *              "updated_at": "2025-01-01 12:00:00",
     *              "farmer": {
     *                  "id": 1,
     *                  "first_name": "John",
     *                  "last_name": "Doe",
     *                  "phone_number": "1234567890",
     *                  "email": "john.doe@example.com",
     *                  "address": "1234567890",
     *                  "city": "Nairobi",
     *                  "state": "Nairobi",
     *                  "zip": "12345",
     *                  "country": "Kenya",
     *                  "created_at": "2025-01-01 12:00:00",
     *                  "updated_at": "2025-01-01 12:00:00"
     *              }
     *          },
     *          "vet": {
     *              "id": 1,
     *              "first_name": "John",
     *              "last_name": "Doe",
     *              "license_number": "1234567890",
     *              "license_expiry_date": "2025-01-01",
     *              "phone_number": "1234567890",
     *              "email": "john.doe@example.com",
     *              "specialization": "Dairy",
     *              "employer": "Dairy Farm",
     *              "milk_collection_center_id": 1,
     *              "bio": "John Doe is a dairy vet with 10 years of experience.",
     *              "is_active": true,
     *              "created_at": "2025-01-01 12:00:00",
     *              "updated_at": "2025-01-01 12:00:00",
     *              "milkCollectionCenter": {
     *                  "id": 1,
     *                  "name": "Dairy Farm",
     *                  "address": "1234567890",
     *                  "phone": "1234567890",
     *                  "email": "dairyfarm@example.com",
     *                  "website": "https://dairyfarm.com",
     *                  "created_at": "2025-01-01 12:00:00",
     *                  "updated_at": "2025-01-01 12:00:00"
     *              }
     *          },
     *          "recordedBy": {
     *              "id": 1,
     *              "name": "John Doe",
     *              "email": "john.doe@example.com",
     *              "created_at": "2025-01-01 12:00:00",
     *              "updated_at": "2025-01-01 12:00:00"
     *          }
     *      }
     *  ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = CowTreatment::query()->with(['cow.farmer', 'vet', 'recordedBy']);

        $cowId = $request->query('cow_id');
        if (!is_null($cowId) && $cowId !== '') {
            $query->where('cow_id', (int) $cowId);
        }

        $vetId = $request->query('vet_id');
        if (!is_null($vetId) && $vetId !== '') {
            $query->where('vet_id', (int) $vetId);
        }

        $status = $request->query('status');
        if (!is_null($status) && $status !== '') {
            $query->where('status', $status);
        }

        $timeline = filter_var($request->query('timeline'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($timeline === true) {
            return response()->json(
                $query->orderByDesc('treatment_date')->get()
            );
        }

        return response()->json(
            $query->orderByDesc('treatment_date')->paginate()->appends($request->query())
        );
    }

     /**
     * Create Cow Treatment
     * @description Create a new cow treatment record.
     * @bodyParam cow_id integer required The ID of the cow.
     * @bodyParam vet_id integer The ID of the vet.
     * @bodyParam recorded_by_id integer The ID of the user who recorded the treatment.
     * @bodyParam treatment_date date required The date of the treatment.
     * @bodyParam diagnosis string The diagnosis of the treatment.
     * @bodyParam reason string The reason for the treatment.
     * @bodyParam medication string The medication given for the treatment.
     * @bodyParam dosage string The dosage of the medication.
     * @bodyParam dosage_unit string The unit of the dosage.
     * @bodyParam route string The route of the treatment.
     * @bodyParam follow_up_date date The date of the follow-up.
     * @bodyParam status string The status of the treatment.
     * @bodyParam outcome string The outcome of the treatment.
     * @bodyParam next_steps string The next steps for the treatment.
     * @bodyParam cost numeric The cost of the treatment.
     * @bodyParam notes string The notes for the treatment.
     * @bodyParam attachment_path string The path to the attachment.
     * @bodyParam life_cycle_status string The life cycle status of the treatment.
     * @response 201 {
     *  "data": {
     *      "id": 1,
     *      "cow_id": 1,
     *      "vet_id": 1,
     *      "status": "active",
     *      "treatment_date": "2025-01-01",
     *      "diagnosis": "Fever",
     *      "reason": "The cow is sick.",
     *      "medication": "Amoxicillin",
     *      "dosage": "100mg",
     *      "dosage_unit": "mg",
     *      "route": "Oral",
     *      "follow_up_date": "2025-01-02",
     *      "status": "active",
     *      "outcome": "Recovered",
     *      "next_steps": "Monitor the cow for 2 weeks.",
     *      "cost": 100000,
     *      "notes": "The cow is recovering well.",
     *      "attachment_path": "https://example.com/attachment.pdf",
     *      "life_cycle_status": "Healthy",
     *      "cow": {
     *          "id": 1,
     *          "tag_number": "1234567890",
     *          "breed": "Holstein",
     *          "date_of_birth": "2020-01-01",
     *          "acquired_date": "2021-01-01",
     *          "health_status": "Healthy",
     *          "milk_capacity_liters": 10000,
     *          "notes": "The cow is healthy and producing milk.",
     *          "created_at": "2025-01-01 12:00:00",
     *          "updated_at": "2025-01-01 12:00:00",
     *          "farmer": {
     *              "id": 1,
     *              "first_name": "John",
     *              "last_name": "Doe",
     *              "phone_number": "1234567890",
     *              "email": "john.doe@example.com",
     *              "address": "1234567890",
     *              "city": "Nairobi",
     *              "state": "Nairobi",
     *              "zip": "12345",
     *              "country": "Kenya",
     *              "created_at": "2025-01-01 12:00:00",
     *              "updated_at": "2025-01-01 12:00:00"
     *          }
     *      }
     *  }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $treatment = CowTreatment::create($data);

        return response()->json($treatment->fresh(['cow.farmer', 'vet', 'recordedBy']), 201);
    }

    /**
     * Get Cow Treatment
     * @description Get a cow treatment record.
     * @urlParam cow_treatment integer required The ID of the cow treatment.
     * @response 200 {
     *  "data": {
     *      "id": 1,
     *      "cow_id": 1,
     *      "vet_id": 1,
     *      "status": "active",
     *      "treatment_date": "2025-01-01",
     *      "diagnosis": "Fever",
     *      "reason": "The cow is sick.",
     *      "medication": "Amoxicillin",
     *      "dosage": "100mg",
     *      "dosage_unit": "mg",
     *      "route": "Oral",
     *      "follow_up_date": "2025-01-02",
     *      "status": "active",
     *      "outcome": "Recovered",
     *      "next_steps": "Monitor the cow for 2 weeks.",
     *      "cost": 100000,
     *      "notes": "The cow is recovering well.",
     *      "attachment_path": "https://example.com/attachment.pdf",
     *      "life_cycle_status": "Healthy",
     *      "cow": {
     *          "id": 1,
     *          "tag_number": "1234567890",
     *          "breed": "Holstein",
     *          "date_of_birth": "2020-01-01",
     *          "acquired_date": "2021-01-01",
     *          "health_status": "Healthy",
     *          "milk_capacity_liters": 10000,
     *          "notes": "The cow is healthy and producing milk.",
     *          "created_at": "2025-01-01 12:00:00",
     *          "updated_at": "2025-01-01 12:00:00",
     *          "farmer": {
     *              "id": 1,
     *              "first_name": "John",
     *              "last_name": "Doe",
     *              "phone_number": "1234567890",
     *              "email": "john.doe@example.com",
     *              "address": "1234567890",
     *              "city": "Nairobi",
     *              "state": "Nairobi",
     *              "zip": "12345",
     *              "country": "Kenya",
     *              "created_at": "2025-01-01 12:00:00",
     *              "updated_at": "2025-01-01 12:00:00"
     *          }
     *      }
     *  }
     * }
     */
    public function show(CowTreatment $cowTreatment): JsonResponse
    {
        return response()->json($cowTreatment->load(['cow.farmer', 'vet', 'recordedBy']));
    }

        /**
     * Update Cow Treatment
     * @description Update a cow treatment record.
     * @bodyParam cow_id integer The ID of the cow.
     * @bodyParam vet_id integer The ID of the vet.
     * @bodyParam recorded_by_id integer The ID of the user who recorded the treatment.
     * @bodyParam treatment_date date The date of the treatment.
     * @bodyParam diagnosis string The diagnosis of the treatment.
     * @bodyParam reason string The reason for the treatment.
     * @bodyParam medication string The medication given for the treatment.
     * @bodyParam dosage string The dosage of the medication.
     * @bodyParam dosage_unit string The unit of the dosage.
     * @bodyParam route string The route of the treatment.
     * @bodyParam follow_up_date date The date of the follow-up.
     * @bodyParam status string The status of the treatment.
     * @bodyParam outcome string The outcome of the treatment.
     * @bodyParam next_steps string The next steps for the treatment.
     * @bodyParam cost numeric The cost of the treatment.
     * @bodyParam notes string The notes for the treatment.
     * @bodyParam attachment_path string The path to the attachment.
     * @bodyParam life_cycle_status string The life cycle status of the treatment.
     * @response 200 {
     *  "data": {
     *      "id": 1,
     *      "cow_id": 1,
     *      "vet_id": 1,
     *      "status": "active",
     *      "treatment_date": "2025-01-01",
     *      "diagnosis": "Fever",
     *      "reason": "The cow is sick.",
     *      "medication": "Amoxicillin",
     *      "dosage": "100mg",
     *      "dosage_unit": "mg",
     *      "route": "Oral",
     *      "follow_up_date": "2025-01-02",
     *      "status": "active",
     *      "outcome": "Recovered",
     *      "next_steps": "Monitor the cow for 2 weeks.",
     *      "cost": 100000,
     *      "notes": "The cow is recovering well.",
     *      "attachment_path": "https://example.com/attachment.pdf",
     *      "life_cycle_status": "Healthy",
     *      "cow": {
     *          "id": 1,
     *          "tag_number": "1234567890",
     *          "breed": "Holstein",
     *          "date_of_birth": "2020-01-01",
     *          "acquired_date": "2021-01-01",
     *          "health_status": "Healthy",
     *          "milk_capacity_liters": 10000,
     *          "notes": "The cow is healthy and producing milk.",
     *          "created_at": "2025-01-01 12:00:00",
     *          "updated_at": "2025-01-01 12:00:00",
     *          "farmer": {
     *              "id": 1,
     *              "first_name": "John",
     *              "last_name": "Doe", 
     *              "phone_number": "1234567890",
     *              "email": "john.doe@example.com",
     *              "address": "1234567890",
     *              "city": "Nairobi",
     *              "state": "Nairobi",
     *              "zip": "12345",
     *              "country": "Kenya",
     *              "created_at": "2025-01-01 12:00:00",
     *              "updated_at": "2025-01-01 12:00:00"
     *          }
     *      }
     *  }
     * }
     */
    public function update(Request $request, CowTreatment $cowTreatment): JsonResponse
    {
        $data = $this->validatedData($request, $cowTreatment->id);

        $cowTreatment->fill($data)->save();

        return response()->json($cowTreatment->fresh(['cow.farmer', 'vet', 'recordedBy']));
    }

    /**
     * Delete Cow Treatment
     * @description Delete a cow treatment record.
     * @urlParam cow_treatment integer required The ID of the cow treatment.
     * @response 204 {
     *  "message": "Cow treatment deleted successfully"
     * }
     */
    public function destroy(CowTreatment $cowTreatment): JsonResponse
    {
        $cowTreatment->delete();

        return response()->json(null, 204);
    }

    protected function validatedData(Request $request, ?int $treatmentId = null): array
    {
        return $request->validate([
            'cow_id' => ['required', 'exists:cows,id'],
            'vet_id' => ['nullable', 'exists:vets,id'],
            'recorded_by_id' => ['nullable', 'exists:users,id'],
            'treatment_date' => ['required', 'date'],
            'diagnosis' => ['nullable', 'string'],
            'reason' => ['nullable', 'string'],
            'medication' => ['nullable', 'string'],
            'dosage' => ['nullable', 'string'],
            'dosage_unit' => ['nullable', 'string', 'max:50'],
            'route' => ['nullable', 'string', 'max:100'],
            'follow_up_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
            'outcome' => ['nullable', 'string', 'max:100'],
            'next_steps' => ['nullable', 'string'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'attachment_path' => ['nullable', 'string', 'max:2048'],
            'life_cycle_status' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
