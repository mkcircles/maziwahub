<?php

use App\Models\FeedingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('lists feeding methods for authenticated users', function (): void {
    FeedingMethod::factory()->count(3)->create();

    $user = User::factory()->create([
        'user_type' => 'admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $response = getJson('/api/v1/feeding-methods');

    $response->assertOk()
        ->assertJsonCount(3);
});

it('allows admins to create a feeding method', function (): void {
    $user = User::factory()->create([
        'user_type' => 'super_admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Cut-and-Carry Fodder',
        'category' => 'cut_and_carry',
        'description' => 'Fresh forage harvested daily and fed indoors.',
        'requires_details' => true,
        'metadata' => [
            'frequency' => 'Twice daily',
        ],
    ];

    $response = postJson('/api/v1/feeding-methods', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Cut-and-Carry Fodder',
            'category' => 'cut_and_carry',
            'requires_details' => true,
        ]);

    expect(FeedingMethod::query()->where('name', 'Cut-and-Carry Fodder')->exists())->toBeTrue();
});

it('prevents non-admin users from creating feeding methods', function (): void {
    $user = User::factory()->create([
        'user_type' => 'user',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $response = postJson('/api/v1/feeding-methods', [
        'name' => 'Pasture Rotation',
    ]);

    $response->assertForbidden();
});

it('updates feeding methods', function (): void {
    $user = User::factory()->create([
        'user_type' => 'admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $method = FeedingMethod::factory()->create([
        'name' => 'Silage',
        'requires_details' => false,
    ]);

    $response = putJson("/api/v1/feeding-methods/{$method->id}", [
        'name' => 'Silage Feeding',
        'requires_details' => true,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'Silage Feeding',
            'requires_details' => true,
        ]);

    expect($method->fresh()->requires_details)->toBeTrue();
});

it('blocks deletion when method is in use', function (): void {
    $user = User::factory()->create([
        'user_type' => 'super_admin',
        'is_active' => true,
    ]);
    Sanctum::actingAs($user);

    $method = FeedingMethod::factory()->create();

    $farmer = \App\Models\Farmer::factory()->create([
        'primary_feeding_method_id' => $method->id,
    ]);

    $response = deleteJson("/api/v1/feeding-methods/{$method->id}");

    $response->assertUnprocessable();
    expect($method->fresh())->not()->toBeNull();
    expect($farmer->fresh()->primary_feeding_method_id)->toBe($method->id);
});


