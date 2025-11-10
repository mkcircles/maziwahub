<?php

use App\Models\Region;
use App\Models\District;
use App\Models\County;
use App\Models\Subcounty;
use App\Models\Parish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('lists districts and filters by region', function (): void {
    $region = Region::factory()->create();
    $otherRegion = Region::factory()->create();

    $district = District::factory()->for($region)->create();
    District::factory()->for($otherRegion)->create();

    $response = getJson('/api/v1/districts?region_id=' . $region->id);

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $district->id,
            'name' => $district->name,
            'region_id' => $region->id,
        ]);
});

it('creates a district for a region', function (): void {
    $region = Region::factory()->create();

    $payload = [
        'name' => 'Kampala',
        'region_id' => $region->id,
        'district_code' => '1234',
    ];

    $response = postJson('/api/v1/districts', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Kampala',
            'region_id' => $region->id,
            'slug' => 'kampala',
        ])
        ->assertJsonPath('region.id', $region->id)
        ->assertJsonPath('region.country.id', $region->country_id)
        ->assertJsonCount(0, 'counties');

    expect(District::query()->where('name', 'Kampala')->where('region_id', $region->id)->exists())->toBeTrue();
});

it('shows a district with its region, country, counties, subcounties and parishes', function (): void {
    $district = District::factory()
        ->has(
            County::factory()
                ->count(2)
                ->has(
                    Subcounty::factory()
                        ->count(1)
                        ->has(Parish::factory()->count(2))
                )
        )
        ->create();

    $response = getJson("/api/v1/districts/{$district->id}");

    $response->assertOk()
        ->assertJsonPath('id', $district->id)
        ->assertJsonPath('region.id', $district->region_id)
        ->assertJsonPath('region.country.id', $district->region->country_id)
        ->assertJsonCount(2, 'counties')
        ->assertJsonCount(1, 'counties.0.subcounties')
        ->assertJsonCount(2, 'counties.0.subcounties.0.parishes');
});

it('updates a district', function (): void {
    $region = Region::factory()->create();
    $otherRegion = Region::factory()->create();
    $district = District::factory()->for($region)->create([
        'name' => 'Old District',
        'district_code' => '5555',
    ]);

    $response = putJson("/api/v1/districts/{$district->id}", [
        'name' => 'New District',
        'region_id' => $otherRegion->id,
        'district_code' => '7777',
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'New District',
            'region_id' => $otherRegion->id,
            'slug' => 'new-district',
        ]);

    $district->refresh();

    expect($district->name)->toBe('New District');
    expect($district->region_id)->toBe($otherRegion->id);
    expect($district->district_code)->toBe('7777');
    expect($district->slug)->toBe('new-district');
});

it('deletes a district', function (): void {
    $district = District::factory()->create();

    $response = deleteJson("/api/v1/districts/{$district->id}");

    $response->assertNoContent();

    expect(District::query()->whereKey($district->id)->exists())->toBeFalse();
});

it('rejects duplicate district names within the same region', function (): void {
    $region = Region::factory()->create();
    District::factory()->for($region)->create(['name' => 'Kampala', 'district_code' => '4321']);

    $response = postJson('/api/v1/districts', [
        'name' => 'Kampala',
        'region_id' => $region->id,
        'district_code' => '9999',
    ]);

    $response->assertUnprocessable();
});

