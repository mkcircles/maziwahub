<?php

namespace Database\Seeders;

use App\Models\MilkCollectionCenter;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Agent 1',
                'email' => 'agent1@maziwahub.com',
                'password' => Hash::make('password'),
                'user_type' => 'agent',
                'milk_collection_center_id' => MilkCollectionCenter::inRandomOrder()->first()->id,
                'is_active' => true,
            ],
            [
                'name' => 'Agent 2',
                'email' => 'agent2@maziwahub.com',
                'password' => Hash::make('password'),
                'user_type' => 'agent',
                'partner_id' => Partner::inRandomOrder()->first()->id,
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

    }
}
