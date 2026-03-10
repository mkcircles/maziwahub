<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MilkCollectionCenter>
 */
class MilkCollectionCenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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

        $ugandanPlaces = [
            'Kampala',
            'Entebbe',
            'Jinja',
            'Mbarara',
            'Gulu',
            'Mbale',
            'Masaka',
            'Kasese',
            'Arua',
            'Lira',
            'Fort Portal',
            'Kabale',
            'Soroti',
            'Hoima',
            'Mukono',
            'Iganga',
            'Tororo',
            'Busia',
            'Mityana',
            'Luweero',
            'Kiruhura',
            'Isingiro',
            'Ntungamo',
            'Rukungiri',
            'Kanungu',
            'Kisoro',
            'Mubende',
            'Kiboga',
            'Nakaseke'
        ];

        $centerNamePrefixes = ['Dairy', 'Milk Collection Center', 'Farmers Cooperative', 'Agri-Hub', 'Milk Coolers', 'Dairy Cooperative'];

        $registrationNumber = $this->faker->unique()->numerify('REG-#####');

        if ($this->faker->boolean(30)) {
            $registrationNumber = null;
        }

        $place = $this->faker->randomElement($ugandanPlaces);
        $prefix = $this->faker->randomElement($centerNamePrefixes);

        return [
            'name' => "{$place} {$prefix}",
            'registration_number' => $registrationNumber,
            'physical_address' => "{$place}, Uganda",
            'latitude' => $this->faker->optional()->latitude(),
            'longitude' => $this->faker->optional()->longitude(),
            'established_date' => $this->faker->optional()->date(),
            'manager_name' => $this->faker->optional()->passthrough(
                $this->faker->randomElement($ugandanFirstNames) . ' ' . $this->faker->randomElement($ugandanLastNames)
            ),
            'manager_phone' => $this->faker->optional()->passthrough(
                '+256 7' . $this->faker->randomElement(['7', '8', '5', '0', '4']) . $this->faker->numerify('#######')
            ),
            'staff_count' => $this->faker->numberBetween(0, 50),
            'power_source' => $this->faker->optional()->randomElement(['Grid', 'Generator', 'Solar']),
            'cooler_capacity_liters' => $this->faker->optional()->numberBetween(500, 5000),
            'has_testing_equipment' => $this->faker->boolean(),
            'has_washing_bay' => $this->faker->boolean(),
        ];
    }
}
