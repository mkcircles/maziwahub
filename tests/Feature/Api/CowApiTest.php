<?php

use App\Models\Cow;
use App\Models\Farmer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

function cowPayload(Farmer $farmer, array $overrides = []): array
{
    return array_merge([
        'farmer_id' => $farmer->id,
        'tag_number' => 'TAG-'.Str::upper(Str::random(5)),
        'breed' => 'Friesian',
        'milk_capacity_liters' => 15.5,
    ], $overrides);
}

it('lists cows filtered by farmer', function (): void {
    $farmer = Farmer::factory()->create();
    $otherFarmer = Farmer::factory()->create();

    Cow::factory()->for($farmer)->create(['tag_number' => 'TAG-001']);
    Cow::factory()->for($otherFarmer)->create(['tag_number' => 'TAG-002']);

    $response = getJson("/api/v1/cows?farmer_id={$farmer->id}");

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['tag_number' => 'TAG-001']);
});

it('creates a cow for a farmer', function (): void {
    $farmer = Farmer::factory()->create();

    $payload = cowPayload($farmer, ['tag_number' => 'TAG-100']);

    $response = postJson('/api/v1/cows', $payload);

    $response->assertCreated()
        ->assertJsonFragment(['tag_number' => 'TAG-100']);

    expect($farmer->cows()->count())->toBe(1);
});

it('updates a cow', function (): void {
    $cow = Cow::factory()->create(['tag_number' => 'TAG-200']);

    $response = putJson("/api/v1/cows/{$cow->id}", [
        'farmer_id' => $cow->farmer_id,
        'tag_number' => 'TAG-201',
        'breed' => 'Ankole',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['tag_number' => 'TAG-201', 'breed' => 'Ankole']);
});

it('deletes a cow', function (): void {
    $cow = Cow::factory()->create();

    $response = deleteJson("/api/v1/cows/{$cow->id}");

    $response->assertNoContent();
    expect(Cow::count())->toBe(0);
});

it('validates unique tag number', function (): void {
    $farmer = Farmer::factory()->create();
    Cow::factory()->create(['tag_number' => 'TAG-999']);

    $response = postJson('/api/v1/cows', cowPayload($farmer, ['tag_number' => 'TAG-999']));

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['tag_number']);
});

