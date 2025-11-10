<?php

use App\Models\Country;
use App\Models\District;
use App\Models\Region;
use App\Models\County;
use App\Models\Subcounty;
use App\Models\Parish;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('lists countries with their regions, districts, counties, subcounties and parishes', function (): void {
    $country = Country::factory()
        ->has(
            Region::factory()
                ->count(2)
                ->has(
                    District::factory()
                        ->count(1)
                        ->has(
                            County::factory()
                                ->count(1)
                                ->has(
                                    Subcounty::factory()
                                        ->count(1)
                                        ->has(
                                            Parish::factory()
                                                ->count(1)
                                                ->has(Village::factory()->count(1))
                                        )
                                )
                        )
                )
        )
        ->create();

    $response = getJson('/api/v1/countries');

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonCount(2, '0.regions')
        ->assertJsonCount(1, '0.regions.0.districts')
        ->assertJsonCount(1, '0.regions.0.districts.0.counties')
        ->assertJsonCount(1, '0.regions.0.districts.0.counties.0.subcounties')
        ->assertJsonCount(1, '0.regions.0.districts.0.counties.0.subcounties.0.parishes')
        ->assertJsonCount(1, '0.regions.0.districts.0.counties.0.subcounties.0.parishes.0.villages')
        ->assertJsonFragment([
            'id' => $country->id,
            'name' => $country->name,
            'iso_code' => $country->iso_code,
        ]);
});

it('creates a country', function (): void {
    $payload = [
        'name' => 'Testland',
        'iso_code' => 'tst',
    ];

    $response = postJson('/api/v1/countries', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Testland',
            'iso_code' => 'TST',
            'slug' => 'testland',
        ]);

    expect(Country::query()->where('iso_code', 'TST')->exists())->toBeTrue();
});

it('shows a single country', function (): void {
    $country = Country::factory()
        ->has(
            Region::factory()
                ->count(1)
                ->has(
                    District::factory()
                        ->count(2)
                        ->has(
                            County::factory()
                                ->count(1)
                                ->has(
                                    Subcounty::factory()
                                        ->count(2)
                                        ->has(
                                            Parish::factory()
                                                ->count(3)
                                                ->has(Village::factory()->count(2))
                                        )
                                )
                        )
                )
        )
        ->create();

    $response = getJson("/api/v1/countries/{$country->id}");

    $response->assertOk()
        ->assertJsonPath('id', $country->id)
        ->assertJsonCount(2, 'regions.0.districts')
        ->assertJsonCount(1, 'regions.0.districts.0.counties')
        ->assertJsonCount(2, 'regions.0.districts.0.counties.0.subcounties')
        ->assertJsonCount(3, 'regions.0.districts.0.counties.0.subcounties.0.parishes')
        ->assertJsonCount(2, 'regions.0.districts.0.counties.0.subcounties.0.parishes.0.villages');
});

it('updates a country', function (): void {
    $country = Country::factory()->create([
        'name' => 'Oldland',
        'iso_code' => 'OLD',
    ]);

    $response = putJson("/api/v1/countries/{$country->id}", [
        'name' => 'Newland',
        'iso_code' => 'new',
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'Newland',
            'iso_code' => 'NEW',
            'slug' => 'newland',
        ]);

    $country->refresh();

    expect($country->name)->toBe('Newland');
    expect($country->iso_code)->toBe('NEW');
    expect($country->slug)->toBe('newland');
});

it('deletes a country', function (): void {
    $country = Country::factory()->create();

    $response = deleteJson("/api/v1/countries/{$country->id}");

    $response->assertNoContent();

    expect(Country::query()->whereKey($country->id)->exists())->toBeFalse();
});

it('rejects duplicate country iso codes', function (): void {
    Country::factory()->create(['iso_code' => 'UGA']);

    $response = postJson('/api/v1/countries', [
        'name' => 'Duplicate',
        'iso_code' => 'uga',
    ]);

    $response->assertUnprocessable();
});

