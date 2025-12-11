<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\MilkCollectionCenter;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = [
            [
                'name' => 'John Kamau',
                'email' => 'john.kamau@maziwahub.com',
                'password' => Hash::make('password'),
                'phone' => '+254712345678',
                'address' => 'Nakuru, Kenya',
                'milk_collection_center_id' => MilkCollectionCenter::inRandomOrder()->first()?->id,
                'partner_id' => null,
            ],
            [
                'name' => 'Sarah Nakato',
                'email' => 'sarah.nakato@maziwahub.com',
                'password' => Hash::make('password'),
                'phone' => '+256700123456',
                'address' => 'Mbarara, Uganda',
                'milk_collection_center_id' => MilkCollectionCenter::inRandomOrder()->first()?->id,
                'partner_id' => null,
            ],
            [
                'name' => 'Peter Omondi',
                'email' => 'peter.omondi@maziwahub.com',
                'password' => Hash::make('password'),
                'phone' => '+254723456789',
                'address' => 'Eldoret, Kenya',
                'milk_collection_center_id' => MilkCollectionCenter::inRandomOrder()->first()?->id,
                'partner_id' => null,
            ],
            [
                'name' => 'Grace Achieng',
                'email' => 'grace.achieng@maziwahub.com',
                'password' => Hash::make('password'),
                'phone' => '+254734567890',
                'address' => 'Kisumu, Kenya',
                'milk_collection_center_id' => null,
                'partner_id' => Partner::inRandomOrder()->first()?->id,
            ],
            [
                'name' => 'David Mugisha',
                'email' => 'david.mugisha@maziwahub.com',
                'password' => Hash::make('password'),
                'phone' => '+256701234567',
                'address' => 'Kampala, Uganda',
                'milk_collection_center_id' => null,
                'partner_id' => Partner::inRandomOrder()->first()?->id,
            ],
        ];

        foreach ($agents as $agentData) {
            // Create the user first
            $user = User::create([
                'name' => $agentData['name'],
                'email' => $agentData['email'],
                'password' => $agentData['password'],
                'user_type' => 'agent',
                'milk_collection_center_id' => $agentData['milk_collection_center_id'],
                'partner_id' => $agentData['partner_id'],
                'is_active' => true,
            ]);
            $user->assignRole($agentData['user_type']);

            // Create the agent record
            Agent::create([
                'user_id' => $user->id,
                'milk_collection_center_id' => $agentData['milk_collection_center_id'],
                'partner_id' => $agentData['partner_id'],
                'phone' => $agentData['phone'],
                'address' => $agentData['address'],
                'is_active' => true,
            ]);
        }
    }
}
