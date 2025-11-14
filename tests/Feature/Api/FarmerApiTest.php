<?php

use App\Models\Farmer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

function farmerPayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone_number' => '0700000000',
        'status' => 'active',
        'is_farmer_insured' => true,
        'coordinates' => [
            'latitude' => 0.3476,
            'longitude' => 32.5825,
        ],
        'family_size' => 5,
        'household_head' => true,
        'financial_instrument' => 'mobile money',
    ], $overrides);
}

it('lists farmers with search and filters', function (): void {
    $jane = Farmer::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe', 'status' => 'active']);
    Farmer::factory()->create(['first_name' => 'Mark', 'status' => 'inactive']);

    $response = getJson('/api/v1/farmers?search=Jane&status=active');

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['first_name' => 'Jane']);
});

it('creates a farmer and generates farmer id', function (): void {
    $response = postJson('/api/v1/farmers', farmerPayload());

    $response->assertCreated()
        ->assertJsonStructure(['farmer_id', 'first_name', 'last_name']);

    expect($response->json('farmer_id'))->toHaveLength(6);
    expect(Farmer::count())->toBe(1);
});

it('updates a farmer details', function (): void {
    $farmer = Farmer::factory()->create();

    $response = putJson("/api/v1/farmers/{$farmer->id}", farmerPayload([
        'status' => 'inactive',
        'financial_instrument' => 'bank',
        'farmer_id' => 'AB12CD',
    ]));

    $response->assertOk()
        ->assertJsonFragment(['status' => 'inactive', 'financial_instrument' => 'bank', 'farmer_id' => 'AB12CD']);

    expect($farmer->fresh()->status)->toBe('inactive');
});

it('deletes a farmer', function (): void {
    $farmer = Farmer::factory()->create();

    $response = deleteJson("/api/v1/farmers/{$farmer->id}");

    $response->assertNoContent();
    expect(Farmer::count())->toBe(0);
});

it('validates unique farmer id', function (): void {
    Farmer::factory()->create(['farmer_id' => 'ABC123']);

    $response = postJson('/api/v1/farmers', farmerPayload(['farmer_id' => 'ABC123']));

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['farmer_id']);
});

