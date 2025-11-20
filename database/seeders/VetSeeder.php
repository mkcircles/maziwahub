<?php

namespace Database\Seeders;

use App\Models\MilkCollectionCenter;
use App\Models\Vet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VetSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $centers = MilkCollectionCenter::query()->pluck('id')->all();
        $userIds = User::query()->pluck('id')->all();

        for ($i = 0; $i < 10; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            Vet::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'license_number' => strtoupper(Str::random(3)) . '-' . $faker->unique()->numerify('######'),
                'license_expiry_date' => $faker->dateTimeBetween('+3 months', '+2 years')->format('Y-m-d'),
                'phone_number' => $faker->phoneNumber(),
                'email' => $faker->unique()->safeEmail(),
                'specialization' => $faker->randomElement([
                    'Dairy Health',
                    'Reproductive Health',
                    'Nutrition',
                    'Infectious Diseases',
                    'Emergency Care',
                ]),
                'employer' => $faker->company(),
                'milk_collection_center_id' => !empty($centers) ? $faker->randomElement($centers) : null,
                'bio' => $faker->sentence(12),
                'is_active' => $faker->boolean(85),
            ]);
        }
    }
}
