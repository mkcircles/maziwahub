<?php

use App\Models\Cow;
use App\Models\CowMilkProduction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

function productionPayload(Cow $cow, array $overrides = []): array
{
    return array_merge([
        'cow_id' => $cow->id,
        'recorded_date' => now()->toDateString(),
        'morning_volume_liters' => 12.5,
        'midday_volume_liters' => 10.0,
        'evening_volume_liters' => 8.75,
    ], $overrides);
}

it('lists cow milk productions filtered by cow', function (): void {
    $cow = Cow::factory()->create();
    $otherCow = Cow::factory()->create();

    CowMilkProduction::factory()->for($cow)->create(['recorded_date' => now()->toDateString()]);
    CowMilkProduction::factory()->for($otherCow)->create(['recorded_date' => now()->subDay()->toDateString()]);

    $response = getJson('/api/v1/cow-milk-productions?cow_id='.$cow->id);

    $response->assertOk()
        ->assertJsonCount(1);
});

it('creates a cow milk production record and calculates total', function (): void {
    $cow = Cow::factory()->create();

    $payload = productionPayload($cow);

    $response = postJson('/api/v1/cow-milk-productions', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'cow_id' => $cow->id,
        ]);

    $expectedTotal = $payload['morning_volume_liters'] + $payload['midday_volume_liters'] + $payload['evening_volume_liters'];

    expect((float) $response->json('total_volume_liters'))->toEqualWithDelta($expectedTotal, 0.01);
    expect(Carbon::parse($response->json('recorded_date'))->toDateString())->toBe(now()->toDateString());
});

it('updates a cow milk production record and updates total', function (): void {
    $production = CowMilkProduction::factory()->create([
        'morning_volume_liters' => 5,
        'midday_volume_liters' => 4,
        'evening_volume_liters' => 3,
        'total_volume_liters' => 12,
    ]);

    $response = putJson('/api/v1/cow-milk-productions/'.$production->id, [
        'cow_id' => $production->cow_id,
        'recorded_date' => $production->recorded_date->toDateString(),
        'morning_volume_liters' => 6,
        'midday_volume_liters' => 5,
        'evening_volume_liters' => 4,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'morning_volume_liters' => '6.00',
            'midday_volume_liters' => '5.00',
            'evening_volume_liters' => '4.00',
        ]);

    expect((float) $response->json('total_volume_liters'))->toEqualWithDelta(15, 0.01);
});

it('prevents duplicate records for the same cow and date', function (): void {
    $cow = Cow::factory()->create();
    CowMilkProduction::factory()->for($cow)->create(['recorded_date' => now()->toDateString()]);

    $response = postJson('/api/v1/cow-milk-productions', productionPayload($cow));

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['recorded_date']);
});

it('deletes a cow milk production record', function (): void {
    $production = CowMilkProduction::factory()->create();

    $response = deleteJson('/api/v1/cow-milk-productions/'.$production->id);

    $response->assertNoContent();
    expect(CowMilkProduction::count())->toBe(0);
});

