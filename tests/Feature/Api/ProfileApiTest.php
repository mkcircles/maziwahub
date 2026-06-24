<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('allows authenticated user to update their profile information', function (): void {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'phone' => '0770000000',
        'location' => 'Original Location',
        'is_active' => true,
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = putJson('/api/v1/auth/profile', [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '0788888888',
        'location' => 'Updated Location',
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '0788888888',
            'location' => 'Updated Location',
        ]);

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@example.com');
    expect($user->phone)->toBe('0788888888');
    expect($user->location)->toBe('Updated Location');
});

it('allows authenticated user to update their password', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('old_password'),
        'is_active' => true,
    ]);

    Sanctum::actingAs($user, ['*']);

    $response = putJson('/api/v1/auth/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new_secure_password',
        'password_confirmation' => 'new_secure_password',
    ]);

    $response->assertOk();

    $user->refresh();
    expect(Hash::check('new_secure_password', $user->password))->toBeTrue();
});

it('validates unique email during profile update', function (): void {
    $user1 = User::factory()->create(['email' => 'user1@example.com', 'is_active' => true]);
    $user2 = User::factory()->create(['email' => 'user2@example.com', 'is_active' => true]);

    Sanctum::actingAs($user1, ['*']);

    $response = putJson('/api/v1/auth/profile', [
        'name' => $user1->name,
        'email' => 'user2@example.com', // Already taken by user2
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});
