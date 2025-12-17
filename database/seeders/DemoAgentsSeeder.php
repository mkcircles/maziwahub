<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\MilkCollectionCenter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAgentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $center = $this->createDemoMCC();
        $this->createDemoAgents($center->id);
    }

    private function createDemoMCC()
    {
        $milkCollectionCenter = MilkCollectionCenter::create([
            'name' => 'Demo Milk Collection Center',
            'registration_number' => 'DEMO-001',
            'physical_address' => 'Demo Address',
            'latitude' => '0.000000',
            'longitude' => '0.000000',
            'location' => 'Demo Location',
            'established_date' => '2025-12-17',
            'manager_name' => 'Demo Manager',
            'manager_phone' => '1234567890',
            'staff_count' => 10,
            'power_source' => 'Solar',
            'cooler_capacity_liters' => 8000,
            'has_testing_equipment' => true,
            'has_washing_bay' => true,
            'partner_id' => null,
        ]);

        return $milkCollectionCenter;
    }

    private function createDemoAgents($mcc)
    {
        $agents = [
            [
                'name' => 'Sulaiman Kakooza',
                'email' => 'sulaimankakooza@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '',
                'address' => 'Gomba, Uganda',
                'milk_collection_center_id' => $mcc,
                'partner_id' => null,
            ],
            [
                'name' => 'Nansubuga Fabiola',
                'email' => 'nansubugafabiola@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '',
                'address' => 'Gomba, Uganda',
                'milk_collection_center_id' => $mcc,
                'partner_id' => null,
            ],
            [
                'name' => 'Mbabazi Fred',
                'email' => 'mbabazifred53@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '',
                'address' => 'Mitooma, Uganda',
                'milk_collection_center_id' => $mcc,
                'partner_id' => null,
            ],
            [
                'name' => 'Daina Tukyereza',
                'email' => 'dainaberries@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '',
                'address' => 'Nakaseke, Uganda',
                'milk_collection_center_id' => $mcc,
                'partner_id' => null,
            ],
            [
                'name' => 'Hakim',
                'email' => 'busibozihakim2@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '',
                'address' => 'Masindi, Uganda',
                'milk_collection_center_id' => $mcc,
                'partner_id' => null,
            ],
            [
                'name' => 'Shamsa Katongole',
                'email' => 'shamusakatongole@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '',
                'address' => 'Gomba, Uganda',
                'milk_collection_center_id' => $mcc,
                'partner_id' => null,
            ],
            [
                'name' => 'Nabbona Disan',
                'email' => 'nabbonadisan@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '',
                'address' => 'Nakaseke, Uganda',
                'milk_collection_center_id' => $mcc,
                'partner_id' => null,
            ],
            [
                'name' => 'Enegrate',
                'email' => 'enegrate@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '',
                'address' => 'Kampala, Uganda',
                'milk_collection_center_id' => $mcc,
                'partner_id' => null,
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
            //$user->assignRole($agentData['user_type']);

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
