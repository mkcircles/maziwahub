<?php

use App\Models\Country;
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

it('lists regions and filters by country', function (): void {
    $country = Country::factory()->create();
    $otherCountry = Country::factory()->create();

    $region = Region::factory()->for($country)->create();
    Region::factory()->for($otherCountry)->create();

    $response = getJson('/api/v1/regions?country_id=' . $country->id);

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $region->id,
            'name' => $region->name,
            'country_id' => $country->id,
        ]);
});

it('creates a region for a country', function (): void {
    $country = Country::factory()->create();

    $payload = [
        'name' => 'Central',
        'country_id' => $country->id,
    ];

    $response = postJson('/api/v1/regions', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Central',
            'country_id' => $country->id,
            'slug' => 'central',
        ])
        ->assertJsonPath('country.id', $country->id)
        ->assertJsonCount(0, 'districts');

    expect(Region::query()->where('name', 'Central')->where('country_id', $country->id)->exists())->toBeTrue();
});

it('shows a region with its country, districts, counties, subcounties and parishes', function (): void {
    $region = Region::factory()
        ->has(
            District::factory()
                ->count(2)
                ->has(
                    County::factory()
                        ->count(1)
                        ->has(
                            Subcounty::factory()
                                ->count(1)
                                ->has(Parish::factory()->count(2))
                        )
                )
        )
        ->create();

    $response = getJson("/api/v1/regions/{$region->id}");

    $response->assertOk()
        ->assertJsonPath('id', $region->id)
        ->assertJsonPath('country.id', $region->country_id)
        ->assertJsonCount(2, 'districts')
        ->assertJsonCount(1, 'districts.0.counties')
        ->assertJsonCount(1, 'districts.0.counties.0.subcounties')
        ->assertJsonCount(2, 'districts.0.counties.0.subcounties.0.parishes');
});

it('updates a region', function (): void {
    $country = Country::factory()->create();
    $otherCountry = Country::factory()->create();
    $region = Region::factory()->for($country)->create([
        'name' => 'Old Region',
    ]);

    $response = putJson("/api/v1/regions/{$region->id}", [
        'name' => 'New Region',
        'country_id' => $otherCountry->id,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'New Region',
            'country_id' => $otherCountry->id,
            'slug' => 'new-region',
        ]);

    $region->refresh();

    expect($region->name)->toBe('New Region');
    expect($region->country_id)->toBe($otherCountry->id);
    expect($region->slug)->toBe('new-region');
});

it('deletes a region', function (): void {
    $region = Region::factory()->create();

    $response = deleteJson("/api/v1/regions/{$region->id}");

    $response->assertNoContent();

    expect(Region::query()->whereKey($region->id)->exists())->toBeFalse();
});

it('rejects duplicate region names within the same country', function (): void {
    $country = Country::factory()->create();
    Region::factory()->for($country)->create(['name' => 'Central']);

    $response = postJson('/api/v1/regions', [
        'name' => 'Central',
        'country_id' => $country->id,
    ]);

    $response->assertUnprocessable();
});

