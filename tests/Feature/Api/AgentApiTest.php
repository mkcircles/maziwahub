<?php

use App\Models\Agent;
use App\Models\MilkCollectionCenter;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

function agentPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Agent Smith',
        'email' => 'smith@maziwahub.com',
        'password' => 'password123',
        'phone' => '0788123456',
        'address' => 'Kampala',
        'is_active' => true,
    ], $overrides);
}

it('allows partner to create agent and automatically sets partner_id', function (): void {
    $partner = Partner::create([
        'name' => 'Lactra Foods',
        'email' => 'hello@lactrafoods.test',
        'is_active' => true,
    ]);

    $partnerUser = User::factory()->create([
        'user_type' => 'partner',
        'partner_id' => $partner->id,
        'is_active' => true,
    ]);

    $mcc = MilkCollectionCenter::factory()->create([
        'partner_id' => $partner->id,
    ]);

    Sanctum::actingAs($partnerUser, ['*']);

    // Call store payload with MCC id, leaving partner_id null (or omitting it)
    $response = postJson('/api/v1/agents', agentPayload([
        'milk_collection_center_id' => $mcc->id,
        'partner_id' => null,
    ]));

    $response->assertCreated();

    $agent = Agent::first();
    expect($agent)->not->toBeNull();
    expect($agent->partner_id)->toBe($partner->id);
    expect($agent->milk_collection_center_id)->toBe($mcc->id);

    // Assert user also gets partner_id
    expect($agent->user->partner_id)->toBe($partner->id);
    expect($agent->user->milk_collection_center_id)->toBe($mcc->id);
});

it('lists agents filtered by partner', function (): void {
    $partner1 = Partner::create([
        'name' => 'Partner One',
        'email' => 'one@partner.com',
        'is_active' => true,
    ]);
    $partner2 = Partner::create([
        'name' => 'Partner Two',
        'email' => 'two@partner.com',
        'is_active' => true,
    ]);

    $partner1User = User::factory()->create([
        'user_type' => 'partner',
        'partner_id' => $partner1->id,
        'is_active' => true,
    ]);

    // Agent belonging to partner 1
    $agent1User = User::factory()->create([
        'name' => 'Agent One',
        'email' => 'agent1@partner.com',
        'user_type' => 'agent', 
        'partner_id' => $partner1->id,
        'is_active' => true,
    ]);
    $agent1 = Agent::create([
        'user_id' => $agent1User->id, 
        'partner_id' => $partner1->id,
        'is_active' => true
    ]);

    // Agent belonging to partner 2
    $agent2User = User::factory()->create([
        'name' => 'Agent Two',
        'email' => 'agent2@partner.com',
        'user_type' => 'agent', 
        'partner_id' => $partner2->id,
        'is_active' => true,
    ]);
    $agent2 = Agent::create([
        'user_id' => $agent2User->id, 
        'partner_id' => $partner2->id,
        'is_active' => true
    ]);

    Sanctum::actingAs($partner1User, ['*']);

    $response = getJson('/api/v1/agents');

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['id' => $agent1->id]);
});
