<?php

use App\Models\County;
use App\Models\District;
use App\Models\Region;
use App\Models\Subcounty;
use App\Models\Parish;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('lists counties and supports filtering', function (): void {
    $district = District::factory()->create();
    $otherDistrict = District::factory()->create();

    $county = County::factory()->for($district)->create();
    County::factory()->for($otherDistrict)->create();

    $response = getJson('/api/v1/counties?district_id='.$district->id);

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $county->id,
            'name' => $county->name,
            'district_id' => $district->id,
        ]);
});

it('creates a county for a district', function (): void {
    $district = District::factory()->create();

    $payload = [
        'name' => 'Central County',
        'district_id' => $district->id,
        'county_code' => '54321',
    ];

    $response = postJson('/api/v1/counties', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Central County',
            'district_id' => $district->id,
            'slug' => 'central-county',
        ])
        ->assertJsonPath('district.id', $district->id)
        ->assertJsonCount(0, 'subcounties');

    expect(County::query()->where('name', 'Central County')->where('district_id', $district->id)->exists())->toBeTrue();
});

it('shows a county with its district, region, country, subcounties, parishes and villages', function (): void {
    $county = County::factory()
        ->has(
            Subcounty::factory()
                ->count(3)
                ->has(
                    Parish::factory()
                        ->count(2)
                        ->has(Village::factory()->count(3))
                )
        )
        ->create();

    $response = getJson("/api/v1/counties/{$county->id}");

    $response->assertOk()
        ->assertJsonPath('id', $county->id)
        ->assertJsonPath('district.id', $county->district_id)
        ->assertJsonPath('district.region.id', $county->district->region_id)
        ->assertJsonPath('district.region.country.id', $county->district->region->country_id)
        ->assertJsonCount(3, 'subcounties')
        ->assertJsonCount(2, 'subcounties.0.parishes')
        ->assertJsonCount(3, 'subcounties.0.parishes.0.villages');
});

it('updates a county', function (): void {
    $district = District::factory()->create();
    $otherDistrict = District::factory()->create();
    $county = County::factory()->for($district)->create([
        'name' => 'Old County',
        'county_code' => '11111',
    ]);

    $response = putJson("/api/v1/counties/{$county->id}", [
        'name' => 'New County',
        'district_id' => $otherDistrict->id,
        'county_code' => '22222',
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'New County',
            'district_id' => $otherDistrict->id,
            'slug' => 'new-county',
        ]);

    $county->refresh();

    expect($county->name)->toBe('New County');
    expect($county->district_id)->toBe($otherDistrict->id);
    expect($county->county_code)->toBe('22222');
    expect($county->slug)->toBe('new-county');
});

it('deletes a county', function (): void {
    $county = County::factory()->create();

    $response = deleteJson("/api/v1/counties/{$county->id}");

    $response->assertNoContent();

    expect(County::query()->whereKey($county->id)->exists())->toBeFalse();
});

it('rejects duplicate county names within the same district', function (): void {
    $district = District::factory()->create();
    County::factory()->for($district)->create(['name' => 'Duplicate County', 'county_code' => '33333']);

    $response = postJson('/api/v1/counties', [
        'name' => 'Duplicate County',
        'district_id' => $district->id,
        'county_code' => '44444',
    ]);

    $response->assertUnprocessable();
});

