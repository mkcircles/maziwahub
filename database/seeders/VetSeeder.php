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

        $ugandanFirstNames = [
            'Kato',
            'Kakuru',
            'Wasswa',
            'Okello',
            'Otim',
            'Mukasa',
            'Kizza',
            'Ssalongo',
            'Kintu',
            'Musisi',
            'Mugisha',
            'Amanya',
            'Tumwebaze',
            'Kansiime',
            'Asiimwe',
            'Atugonza',
            'Byaruhanga',
            'Twinamasiko',
            'Babirye',
            'Nakato',
            'Namukasa',
            'Nnakintu',
            'Nassiwa',
            'Nalongo',
            'Akello',
            'Aber',
            'Apiyo',
            'Kemigisha',
            'Nantongo',
            'Nabayego',
            'Kobusingye',
            'Natukunda',
            'Namatovu'
        ];

        $ugandanLastNames = [
            'Ssekandi',
            'Kibirige',
            'Ssemwogerere',
            'Nsibambi',
            'Kuteesa',
            'Opondo',
            'Ochola',
            'Musinguzi',
            'Tumwine',
            'Kyagulanyi',
            'Museveni',
            'Besigye',
            'Bwanika',
            'Mbidde',
            'Lule',
            'Binaisa',
            'Obote',
            'Amin',
            'Okurut',
            'Aceng',
            'Mwesigye',
            'Ruhakana',
            'Rugunda',
            'Kataha',
            'Karusoke'
        ];

        for ($i = 0; $i < 10; $i++) {
            $firstName = $faker->randomElement($ugandanFirstNames);
            $lastName = $faker->randomElement($ugandanLastNames);

            Vet::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'license_number' => strtoupper(Str::random(3)) . '-' . $faker->unique()->numerify('######'),
                'license_expiry_date' => $faker->dateTimeBetween('+3 months', '+2 years')->format('Y-m-d'),
                'phone_number' => '+256 7' . $faker->randomElement(['7', '8', '5', '0', '4']) . $faker->numerify('#######'),
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
