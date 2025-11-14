<?php

use App\Models\Farmer;
use App\Models\MilkCollectionCenter;
use App\Models\MilkDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

function deliveryPayload(Farmer $farmer, MilkCollectionCenter $center, array $overrides = []): array
{
    return array_merge([
        'farmer_id' => $farmer->id,
        'milk_collection_center_id' => $center->id,
        'delivery_date' => now()->toDateString(),
        'volume_liters' => 120.5,
        'price_per_liter' => 750,
        'quality_grade' => 'A',
    ], $overrides);
}

it('lists deliveries filtered by farmer', function (): void {
    $farmer = Farmer::factory()->create();
    $center = MilkCollectionCenter::factory()->create();
    $otherFarmer = Farmer::factory()->create();

    MilkDelivery::factory()->create(['farmer_id' => $farmer->id, 'milk_collection_center_id' => $center->id]);
    MilkDelivery::factory()->create(['farmer_id' => $otherFarmer->id, 'milk_collection_center_id' => $center->id]);

    $response = getJson("/api/v1/milk-deliveries?farmer_id={$farmer->id}");

    $response->assertOk()
        ->assertJsonCount(1);
});

it('creates a milk delivery and calculates total', function (): void {
    $farmer = Farmer::factory()->create();
    $center = MilkCollectionCenter::factory()->create();

    $payload = deliveryPayload($farmer, $center);

    $response = postJson('/api/v1/milk-deliveries', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'farmer_id' => $farmer->id,
            'milk_collection_center_id' => $center->id,
        ]);

    expect((float) $response->json('total_amount'))->toEqualWithDelta($payload['volume_liters'] * $payload['price_per_liter'], 0.01);
});

it('updates a milk delivery', function (): void {
    $delivery = MilkDelivery::factory()->create([
        'volume_liters' => 50,
        'price_per_liter' => 700,
    ]);

    $response = putJson("/api/v1/milk-deliveries/{$delivery->id}", [
        'farmer_id' => $delivery->farmer_id,
        'milk_collection_center_id' => $delivery->milk_collection_center_id,
        'delivery_date' => now()->toDateString(),
        'volume_liters' => 60,
        'price_per_liter' => 800,
    ]);

    $response->assertOk()
        ->assertJsonPath('volume_liters', '60.00')
        ->assertJsonPath('price_per_liter', '800.00');

    expect((float) $response->json('total_amount'))->toEqualWithDelta(60 * 800, 0.01);
});

it('deletes a milk delivery', function (): void {
    $delivery = MilkDelivery::factory()->create();

    $response = deleteJson("/api/v1/milk-deliveries/{$delivery->id}");

    $response->assertNoContent();
    expect(MilkDelivery::count())->toBe(0);
});

