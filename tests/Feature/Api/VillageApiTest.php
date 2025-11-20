<?php

use App\Models\Parish;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('lists villages and supports filtering', function (): void {
    $parish = Parish::factory()->create();
    $otherParish = Parish::factory()->create();

    $village = Village::factory()->for($parish)->create();
    Village::factory()->for($otherParish)->create();

    $response = getJson('/api/v1/villages?parish_id='.$parish->id);

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $village->id,
            'name' => $village->name,
            'parish_id' => $parish->id,
        ]);
});

it('creates a village for a parish', function (): void {
    $parish = Parish::factory()->create();

    $payload = [
        'name' => 'Central Cell',
        'parish_id' => $parish->id,
        'village_code' => '9999999',
    ];

    $response = postJson('/api/v1/villages', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Central Cell',
            'parish_id' => $parish->id,
            'slug' => 'central-cell',
        ])
        ->assertJsonPath('parish.id', $parish->id)
        ->assertJsonPath('parish.subcounty.id', $parish->subcounty_id);

    expect(Village::query()->where('village_name', 'Central Cell')->where('parish_id', $parish->id)->exists())->toBeTrue();
});

it('shows a village with its parish, subcounty, district, region and country', function (): void {
    $village = Village::factory()->create();

    $response = getJson("/api/v1/villages/{$village->id}");

    $response->assertOk()
        ->assertJsonPath('id', $village->id)
        ->assertJsonPath('parish.id', $village->parish_id)
        ->assertJsonPath('parish.subcounty.id', $village->parish->subcounty_id)
        ->assertJsonPath('parish.subcounty.county.id', $village->parish->subcounty->county_id)
        ->assertJsonPath('parish.subcounty.county.district.id', $village->parish->subcounty->county->district_id)
        ->assertJsonPath('parish.subcounty.county.district.region.id', $village->parish->subcounty->county->district->region_id)
        ->assertJsonPath('parish.subcounty.county.district.region.country.id', $village->parish->subcounty->county->district->region->country_id);
});

it('updates a village', function (): void {
    $parish = Parish::factory()->create();
    $otherParish = Parish::factory()->create();
    $village = Village::factory()->for($parish)->create([
        'name' => 'Old Village',
        'village_code' => '1010101',
    ]);

    $response = putJson("/api/v1/villages/{$village->id}", [
        'name' => 'New Village',
        'parish_id' => $otherParish->id,
        'village_code' => '2020202',
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'New Village',
            'parish_id' => $otherParish->id,
            'slug' => 'new-village',
        ]);

    $village->refresh();

    expect($village->name)->toBe('New Village');
    expect($village->parish_id)->toBe($otherParish->id);
    expect($village->village_code)->toBe('2020202');
    expect($village->slug)->toBe('new-village');
});

it('deletes a village', function (): void {
    $village = Village::factory()->create();

    $response = deleteJson("/api/v1/villages/{$village->id}");

    $response->assertNoContent();

    expect(Village::query()->whereKey($village->id)->exists())->toBeFalse();
});

it('rejects duplicate village names within the same parish', function (): void {
    $parish = Parish::factory()->create();
    Village::factory()->for($parish)->create(['name' => 'Duplicate Village', 'village_code' => '3030303']);

    $response = postJson('/api/v1/villages', [
        'name' => 'Duplicate Village',
        'parish_id' => $parish->id,
        'village_code' => '4040404',
    ]);

    $response->assertUnprocessable();
});

