<?php

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

it('lists subcounties and supports filtering', function (): void {
    $county = County::factory()->create();
    $otherCounty = County::factory()->create();

    $subcounty = Subcounty::factory()->for($county)->create();
    Subcounty::factory()->for($otherCounty)->create();

    $response = getJson('/api/v1/subcounties?county_id='.$county->id);

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $subcounty->id,
            'name' => $subcounty->name,
            'county_id' => $county->id,
        ]);
});

it('creates a subcounty for a county', function (): void {
    $county = County::factory()->create();

    $payload = [
        'name' => 'North Division',
        'county_id' => $county->id,
        'subcounty_code' => '888888',
    ];

    $response = postJson('/api/v1/subcounties', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'North Division',
            'county_id' => $county->id,
            'slug' => 'north-division',
        ])
        ->assertJsonPath('county.id', $county->id)
        ->assertJsonPath('county.district.id', $county->district_id)
        ->assertJsonCount(0, 'parishes');

    expect(Subcounty::query()->where('subcounty_name', 'North Division')->where('county_id', $county->id)->exists())->toBeTrue();
});

it('shows a subcounty with its county, district, region, country, parishes and villages', function (): void {
    $subcounty = Subcounty::factory()
        ->has(
            Parish::factory()
                ->count(4)
                ->has(Village::factory()->count(2))
        )
        ->create();

    $response = getJson("/api/v1/subcounties/{$subcounty->id}");

    $response->assertOk()
        ->assertJsonPath('id', $subcounty->id)
        ->assertJsonPath('county.id', $subcounty->county_id)
        ->assertJsonPath('county.district.id', $subcounty->county->district_id)
        ->assertJsonPath('county.district.region.id', $subcounty->county->district->region_id)
        ->assertJsonPath('county.district.region.country.id', $subcounty->county->district->region->country_id)
        ->assertJsonCount(4, 'parishes')
        ->assertJsonCount(2, 'parishes.0.villages');
});

it('updates a subcounty', function (): void {
    $county = County::factory()->create();
    $otherCounty = County::factory()->create();
    $subcounty = Subcounty::factory()->for($county)->create([
        'name' => 'Old Subcounty',
        'subcounty_code' => '101010',
    ]);

    $response = putJson("/api/v1/subcounties/{$subcounty->id}", [
        'name' => 'New Subcounty',
        'county_id' => $otherCounty->id,
        'subcounty_code' => '202020',
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'New Subcounty',
            'county_id' => $otherCounty->id,
            'slug' => 'new-subcounty',
        ]);

    $subcounty->refresh();

    expect($subcounty->name)->toBe('New Subcounty');
    expect($subcounty->county_id)->toBe($otherCounty->id);
    expect($subcounty->subcounty_code)->toBe('202020');
    expect($subcounty->slug)->toBe('new-subcounty');
});

it('deletes a subcounty', function (): void {
    $subcounty = Subcounty::factory()->create();

    $response = deleteJson("/api/v1/subcounties/{$subcounty->id}");

    $response->assertNoContent();

    expect(Subcounty::query()->whereKey($subcounty->id)->exists())->toBeFalse();
});

it('rejects duplicate subcounty names within the same county', function (): void {
    $county = County::factory()->create();
    Subcounty::factory()->for($county)->create(['name' => 'Duplicate Subcounty', 'subcounty_code' => '303030']);

    $response = postJson('/api/v1/subcounties', [
        'name' => 'Duplicate Subcounty',
        'county_id' => $county->id,
        'subcounty_code' => '404040',
    ]);

    $response->assertUnprocessable();
});

