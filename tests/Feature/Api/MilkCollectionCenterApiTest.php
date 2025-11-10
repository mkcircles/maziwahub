<?php

use App\Models\MilkCollectionCenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('lists milk collection centers with optional filtering', function (): void {
    MilkCollectionCenter::factory()->create([
        'name' => 'Testing Facility',
        'has_testing_equipment' => true,
    ]);

    MilkCollectionCenter::factory()->create([
        'name' => 'Cooling Only',
        'has_testing_equipment' => false,
    ]);

    $response = getJson('/api/v1/milk-collection-centers?has_testing_equipment=true');

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'Testing Facility']);
});

it('creates a milk collection center', function (): void {
    $payload = [
        'name' => 'Central Milk Hub',
        'registration_number' => 'REG-12345',
        'physical_address' => 'Plot 10 Dairy Lane',
        'latitude' => 0.347596,
        'longitude' => 32.582520,
        'established_date' => '2020-01-15',
        'manager_name' => 'Jane Manager',
        'manager_phone' => '+256770000000',
        'staff_count' => 12,
        'power_source' => 'Solar',
        'cooler_capacity_liters' => 1500,
        'has_testing_equipment' => true,
        'has_washing_bay' => true,
    ];

    $response = postJson('/api/v1/milk-collection-centers', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Central Milk Hub',
            'registration_number' => 'REG-12345',
            'physical_address' => 'Plot 10 Dairy Lane',
        ]);

    expect(MilkCollectionCenter::query()->where('name', 'Central Milk Hub')->exists())->toBeTrue();
});

it('shows a milk collection center', function (): void {
    $center = MilkCollectionCenter::factory()->create([
        'name' => 'Regional Depot',
        'registration_number' => 'REG-67890',
    ]);

    $response = getJson("/api/v1/milk-collection-centers/{$center->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $center->id,
            'name' => 'Regional Depot',
            'registration_number' => 'REG-67890',
        ]);
});

it('updates a milk collection center', function (): void {
    $center = MilkCollectionCenter::factory()->create([
        'name' => 'Old Depot',
        'registration_number' => 'REG-11111',
    ]);

    $response = putJson("/api/v1/milk-collection-centers/{$center->id}", [
        'name' => 'New Depot',
        'registration_number' => 'REG-22222',
        'physical_address' => 'New Address',
        'has_testing_equipment' => true,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'New Depot',
            'registration_number' => 'REG-22222',
            'physical_address' => 'New Address',
            'has_testing_equipment' => true,
        ]);

    $center->refresh();

    expect($center->name)->toBe('New Depot');
    expect($center->registration_number)->toBe('REG-22222');
    expect($center->has_testing_equipment)->toBeTrue();
});

it('deletes a milk collection center', function (): void {
    $center = MilkCollectionCenter::factory()->create();

    $response = deleteJson("/api/v1/milk-collection-centers/{$center->id}");

    $response->assertNoContent();

    expect(MilkCollectionCenter::query()->whereKey($center->id)->exists())->toBeFalse();
});

it('validates uniqueness of name and registration number', function (): void {
    MilkCollectionCenter::factory()->create([
        'name' => 'Duplicate Name',
        'registration_number' => 'REG-99999',
    ]);

    $response = postJson('/api/v1/milk-collection-centers', [
        'name' => 'Duplicate Name',
        'registration_number' => 'REG-99999',
        'physical_address' => 'Address',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'registration_number']);
});

