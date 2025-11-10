<?php

use App\Models\Parish;
use App\Models\Subcounty;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('lists parishes and supports filtering', function (): void {
    $subcounty = Subcounty::factory()->create();
    $otherSubcounty = Subcounty::factory()->create();

    $parish = Parish::factory()->for($subcounty)->create();
    Parish::factory()->for($otherSubcounty)->create();

    $response = getJson('/api/v1/parishes?subcounty_id='.$subcounty->id);

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'id' => $parish->id,
            'name' => $parish->name,
            'subcounty_id' => $subcounty->id,
        ]);
});

it('creates a parish for a subcounty', function (): void {
    $subcounty = Subcounty::factory()->create();

    $payload = [
        'name' => 'Central Ward',
        'subcounty_id' => $subcounty->id,
        'parish_code' => '7777777',
    ];

    $response = postJson('/api/v1/parishes', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Central Ward',
            'subcounty_id' => $subcounty->id,
            'slug' => 'central-ward',
        ])
        ->assertJsonPath('subcounty.id', $subcounty->id)
        ->assertJsonPath('subcounty.county.id', $subcounty->county_id)
        ->assertJsonCount(0, 'villages');

    expect(Parish::query()->where('name', 'Central Ward')->where('subcounty_id', $subcounty->id)->exists())->toBeTrue();
});

it('shows a parish with its subcounty, county, district, region, country and villages', function (): void {
    $parish = Parish::factory()
        ->has(Village::factory()->count(3))
        ->create();

    $response = getJson("/api/v1/parishes/{$parish->id}");

    $response->assertOk()
        ->assertJsonPath('id', $parish->id)
        ->assertJsonPath('subcounty.id', $parish->subcounty_id)
        ->assertJsonPath('subcounty.county.id', $parish->subcounty->county_id)
        ->assertJsonPath('subcounty.county.district.id', $parish->subcounty->county->district_id)
        ->assertJsonPath('subcounty.county.district.region.id', $parish->subcounty->county->district->region_id)
        ->assertJsonPath('subcounty.county.district.region.country.id', $parish->subcounty->county->district->region->country_id)
        ->assertJsonCount(3, 'villages');
});

it('updates a parish', function (): void {
    $subcounty = Subcounty::factory()->create();
    $otherSubcounty = Subcounty::factory()->create();
    $parish = Parish::factory()->for($subcounty)->create([
        'name' => 'Old Parish',
        'parish_code' => '9090909',
    ]);

    $response = putJson("/api/v1/parishes/{$parish->id}", [
        'name' => 'New Parish',
        'subcounty_id' => $otherSubcounty->id,
        'parish_code' => '8080808',
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'New Parish',
            'subcounty_id' => $otherSubcounty->id,
            'slug' => 'new-parish',
        ]);

    $parish->refresh();

    expect($parish->name)->toBe('New Parish');
    expect($parish->subcounty_id)->toBe($otherSubcounty->id);
    expect($parish->parish_code)->toBe('8080808');
    expect($parish->slug)->toBe('new-parish');
});

it('deletes a parish', function (): void {
    $parish = Parish::factory()->create();

    $response = deleteJson("/api/v1/parishes/{$parish->id}");

    $response->assertNoContent();

    expect(Parish::query()->whereKey($parish->id)->exists())->toBeFalse();
});

it('rejects duplicate parish names within the same subcounty', function (): void {
    $subcounty = Subcounty::factory()->create();
    Parish::factory()->for($subcounty)->create(['name' => 'Duplicate Parish', 'parish_code' => '6060606']);

    $response = postJson('/api/v1/parishes', [
        'name' => 'Duplicate Parish',
        'subcounty_id' => $subcounty->id,
        'parish_code' => '5050505',
    ]);

    $response->assertUnprocessable();
});

