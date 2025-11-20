<?php

use App\Models\Farmer;
use App\Models\FarmerFeedingHistory;
use App\Models\FeedingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('records a feeding history entry and updates farmer snapshot', function (): void {
    $farmer = Farmer::factory()->create();
    $method = FeedingMethod::factory()->create();

    $user = User::factory()->create([
        'user_type' => 'admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $response = postJson("/api/v1/farmers/{$farmer->id}/feeding-history", [
        'feeding_type' => 'primary',
        'feeding_method_id' => $method->id,
        'notes' => 'Switched to high-quality forage.',
        'metadata' => ['frequency' => 'Twice daily'],
        'feeding_notes' => 'Currently on pasture rotation with supplemental hay.',
    ]);

    $response->assertCreated()
        ->assertJsonFragment([
            'feeding_type' => 'primary',
            'notes' => 'Switched to high-quality forage.',
        ]);

    $farmer->refresh();

    expect($farmer->primary_feeding_method_id)->toBe($method->id);
    expect($farmer->feeding_notes)->toContain('Currently on pasture rotation');
    expect(FarmerFeedingHistory::query()->where('farmer_id', $farmer->id)->count())->toBe(1);
});

it('lists feeding history entries for a farmer', function (): void {
    $history = FarmerFeedingHistory::factory()->create([
        'notes' => 'Initial feeding regime',
    ]);

    $user = User::factory()->create([
        'user_type' => 'admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $response = getJson("/api/v1/farmers/{$history->farmer_id}/feeding-history");

    $response->assertOk()
        ->assertJsonFragment(['notes' => 'Initial feeding regime']);
});

it('allows recording custom feeding entries without predefined method', function (): void {
    $farmer = Farmer::factory()->create();

    $user = User::factory()->create([
        'user_type' => 'admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $response = postJson("/api/v1/farmers/{$farmer->id}/feeding-history", [
        'feeding_type' => 'other',
        'notes' => 'Temporary drought ration using cactus feed.',
    ]);

    $response->assertCreated()
        ->assertJsonFragment([
            'feeding_type' => 'other',
            'notes' => 'Temporary drought ration using cactus feed.',
        ]);

    expect(FarmerFeedingHistory::query()->where('farmer_id', $farmer->id)->count())->toBe(1);
});

it('updates and closes an active feeding history record', function (): void {
    $history = FarmerFeedingHistory::factory()->create([
        'started_at' => now()->subMonth(),
        'ended_at' => null,
    ]);

    $user = User::factory()->create([
        'user_type' => 'admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $response = putJson("/api/v1/farmers/{$history->farmer_id}/feeding-history/{$history->id}", [
        'feeding_type' => $history->feeding_type,
        'feeding_method_id' => $history->feeding_method_id,
        'ended_at' => now()->toISOString(),
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $history->id,
        ]);

    $history->refresh();
    expect($history->ended_at)->not()->toBeNull();
});

it('deletes a feeding history entry and refreshes farmer snapshot', function (): void {
    $method = FeedingMethod::factory()->create();
    $history = FarmerFeedingHistory::factory()->create([
        'feeding_method_id' => $method->id,
        'feeding_type' => 'primary',
        'ended_at' => null,
    ]);

    $farmer = $history->farmer;
    $farmer->update(['primary_feeding_method_id' => $method->id]);

    $user = User::factory()->create([
        'user_type' => 'admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $response = deleteJson("/api/v1/farmers/{$history->farmer_id}/feeding-history/{$history->id}");

    $response->assertNoContent();

    expect(FarmerFeedingHistory::query()->whereKey($history->id)->exists())->toBeFalse();
    expect($farmer->fresh()->primary_feeding_method_id)->toBeNull();
});


