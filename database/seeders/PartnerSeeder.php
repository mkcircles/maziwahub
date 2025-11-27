<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\MilkCollectionCenter;
use App\Models\MilkCollectionCenterClaim;
use App\Models\Partner;
use App\Models\PartnerInvitation;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerSeeder extends Seeder
{
    /**
     * Seed partner demo data with users, invitations, and MCC claims.
     */
    public function run(): void
    {
        $faker = FakerFactory::create();
        $superAdmin = User::where('user_type', 'super_admin')->first();

        $partnersConfig = [
            [
                'name' => 'Lactra Foods Cooperative',
                'email' => 'hello@lactrafoods.test',
                'registration_number' => 'PF-2025-001',
                'phone' => '+256 700 123456',
                'address' => 'Plot 12 Dairy Lane, Kampala',
                'description' => 'Regional aggregator focusing on central Uganda dairy producers.',
                'contact_name' => 'Grace Nakabugo',
                'contact_title' => 'Operations Director',
                'website' => 'https://lactrafoods.test',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'postal_code' => '25601',
                'assigned_centers' => 3,
                'invitations' => [
                    [
                        'email' => 'ops@lactrafoods.test',
                        'name' => 'Samuel Nsubuga',
                        'role' => 'partner_admin',
                        'status' => PartnerInvitation::STATUS_PENDING,
                    ],
                    [
                        'email' => 'quality@lactrafoods.test',
                        'name' => 'Esther Baluku',
                        'role' => 'partner_agent',
                        'status' => PartnerInvitation::STATUS_ACCEPTED,
                    ],
                ],
                'claims' => [
                    [
                        'status' => MilkCollectionCenterClaim::STATUS_PENDING,
                        'message' => 'Expanding reception capacity in Wakiso.',
                        'center' => 'unassigned',
                    ],
                    [
                        'status' => MilkCollectionCenterClaim::STATUS_APPROVED,
                        'message' => 'Primary bulking station approved.',
                        'center' => 'owned',
                    ],
                ],
            ],
            [
                'name' => 'Nile Dairy Collective',
                'email' => 'contact@niledairy.test',
                'registration_number' => 'PF-2025-002',
                'phone' => '+256 703 442210',
                'address' => 'Riverfront Business Park, Jinja',
                'description' => 'Collective of eastern corridor farmers supplying premium milk.',
                'contact_name' => 'Peter Owor',
                'contact_title' => 'Head of Partnerships',
                'website' => 'https://niledairy.test',
                'city' => 'Jinja',
                'country' => 'Uganda',
                'postal_code' => '43001',
                'assigned_centers' => 4,
                'invitations' => [
                    [
                        'email' => 'finance@niledairy.test',
                        'name' => 'Linda Kobusingye',
                        'role' => 'partner_admin',
                        'status' => PartnerInvitation::STATUS_PENDING,
                    ],
                    [
                        'email' => 'field@niledairy.test',
                        'name' => 'Dan Ssenyonga',
                        'role' => 'partner_agent',
                        'status' => PartnerInvitation::STATUS_REVOKED,
                    ],
                ],
                'claims' => [
                    [
                        'status' => MilkCollectionCenterClaim::STATUS_PENDING,
                        'message' => 'Request to manage community-run MCC.',
                        'center' => 'unassigned',
                    ],
                    [
                        'status' => MilkCollectionCenterClaim::STATUS_REJECTED,
                        'message' => 'Documentation incomplete during review.',
                        'center' => 'unassigned',
                    ],
                ],
            ],
            [
                'name' => 'Highland Creameries',
                'email' => 'info@highlandcreameries.test',
                'registration_number' => 'PF-2025-003',
                'phone' => '+256 701 889900',
                'address' => '1 Summit Close, Kabale',
                'description' => 'Smallholder-focused cooperative rooted in the Kigezi highlands.',
                'contact_name' => 'Maria Atukunda',
                'contact_title' => 'Programs Lead',
                'website' => 'https://highlandcreameries.test',
                'city' => 'Kabale',
                'country' => 'Uganda',
                'postal_code' => '33002',
                'assigned_centers' => 2,
                'invitations' => [
                    [
                        'email' => 'support@highlandcreameries.test',
                        'name' => 'Ronald Byaruhanga',
                        'role' => 'partner_agent',
                        'status' => PartnerInvitation::STATUS_PENDING,
                    ],
                ],
                'claims' => [
                    [
                        'status' => MilkCollectionCenterClaim::STATUS_PENDING,
                        'message' => 'Seeking approval to connect a rural collection point.',
                        'center' => 'unassigned',
                    ],
                ],
            ],
        ];

        foreach ($partnersConfig as $config) {
            if (Partner::where('email', $config['email'])->exists()) {
                continue;
            }

            $partner = Partner::create(Arr::only($config, [
                'name',
                'email',
                'phone',
                'address',
                'registration_number',
                'description',
                'contact_name',
                'contact_title',
                'website',
                'city',
                'country',
                'postal_code',
            ]) + ['is_active' => true]);

            $partnerAdmin = User::factory()->create([
                'name' => $config['contact_name'],
                'email' => 'admin@' . Str::slug($partner->name) . '.test',
                'user_type' => 'partner',
                'partner_id' => $partner->id,
                'is_active' => true,
            ]);

            $agentUser = User::factory()->create([
                'name' => $faker->name(),
                'email' => 'agent@' . Str::slug($partner->name) . '.test',
                'user_type' => 'partner',
                'partner_id' => $partner->id,
                'is_active' => true,
            ]);

            $assignedCenters = MilkCollectionCenter::whereNull('partner_id')
                ->inRandomOrder()
                ->take($config['assigned_centers'])
                ->get();

            foreach ($assignedCenters as $center) {
                $center->update(['partner_id' => $partner->id]);
            }

            $agent = Agent::create([
                'user_id' => $agentUser->id,
                'partner_id' => $partner->id,
                'milk_collection_center_id' => $assignedCenters->first()->id ?? null,
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'is_active' => true,
            ]);

            foreach ($config['invitations'] as $inviteConfig) {
                $invitation = $partner->invitations()->create([
                    'email' => $inviteConfig['email'],
                    'name' => $inviteConfig['name'],
                    'role' => $inviteConfig['role'],
                    'status' => PartnerInvitation::STATUS_PENDING,
                    'invited_by_user_id' => optional($superAdmin)->id ?? $partnerAdmin->id,
                    'notes' => $faker->sentence(),
                    'expires_at' => now()->addDays(10),
                ]);

                if ($inviteConfig['status'] === PartnerInvitation::STATUS_ACCEPTED) {
                    $invitation->markAccepted();
                } elseif ($inviteConfig['status'] === PartnerInvitation::STATUS_REVOKED) {
                    $invitation->markRevoked();
                }
            }

            foreach ($config['claims'] as $claimConfig) {
                $claimCenter = null;

                if ($claimConfig['center'] === 'owned' && $assignedCenters->isNotEmpty()) {
                    $claimCenter = $assignedCenters->random();
                }

                if (! $claimCenter) {
                    $claimCenter = MilkCollectionCenter::whereNull('partner_id')->inRandomOrder()->first()
                        ?? MilkCollectionCenter::factory()->create([
                            'physical_address' => $faker->address(),
                        ]);
                }

                $claim = MilkCollectionCenterClaim::create([
                    'milk_collection_center_id' => $claimCenter->id,
                    'partner_id' => $partner->id,
                    'requested_by_user_id' => $partnerAdmin->id,
                    'status' => $claimConfig['status'],
                    'message' => $claimConfig['message'],
                    'responded_by_user_id' => in_array($claimConfig['status'], [
                        MilkCollectionCenterClaim::STATUS_APPROVED,
                        MilkCollectionCenterClaim::STATUS_REJECTED,
                    ], true) ? optional($superAdmin)->id ?? $partnerAdmin->id : null,
                    'responded_at' => in_array($claimConfig['status'], [
                        MilkCollectionCenterClaim::STATUS_APPROVED,
                        MilkCollectionCenterClaim::STATUS_REJECTED,
                    ], true) ? now()->subDays(rand(1, 5)) : null,
                    'response_notes' => $claimConfig['status'] === MilkCollectionCenterClaim::STATUS_APPROVED
                        ? 'Approved as part of expansion programme.'
                        : ($claimConfig['status'] === MilkCollectionCenterClaim::STATUS_REJECTED
                            ? 'Request declined: ' . $claimConfig['message']
                            : null),
                ]);

                if ($claimConfig['status'] === MilkCollectionCenterClaim::STATUS_APPROVED) {
                    $claimCenter->update(['partner_id' => $partner->id]);
                }
            }
        }
    }
}



