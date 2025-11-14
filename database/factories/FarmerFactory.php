<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Farmer>
 */
class FarmerFactory extends Factory
{
    public function definition(): array
    {
        $householdHead = $this->faker->boolean();
        $familySize = $this->faker->numberBetween(1, 10);
        $maleMembers = $this->faker->numberBetween(0, $familySize);
        $femaleMembers = max($familySize - $maleMembers, 0);

        $farmerId = $this->generateFarmerId();

        return [
            'farmer_id' => $farmerId,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'dob' => $this->faker->optional()->dateTimeBetween('-70 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'educational_level' => $this->faker->randomElement(['None', 'Primary', 'Secondary', 'Tertiary']),
            'phone_number' => $this->faker->phoneNumber(),
            'id_number' => $this->faker->optional()->uuid(),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'district' => $this->faker->city(),
            'county' => $this->faker->citySuffix(),
            'sub_county' => $this->faker->streetName(),
            'parish' => $this->faker->word(),
            'village' => $this->faker->word(),
            'next_of_kin' => $this->faker->name(),
            'next_of_kin_contact' => $this->faker->phoneNumber(),
            'next_of_kin_relationship' => $this->faker->randomElement(['spouse', 'brother', 'sister', 'parent']),
            'no_of_household_members' => $familySize,
            'registered_by' => $this->faker->name(),
            'photo' => null,
            'coordinates' => [
                'latitude' => $this->faker->latitude(),
                'longitude' => $this->faker->longitude(),
            ],
            'status' => $this->faker->randomElement(['pending', 'active', 'inactive']),
            'validation_reason' => null,
            'reg_type' => $this->faker->randomElement(['individual', 'cooperative']),
            'household_head' => $householdHead,
            'family_size' => $familySize,
            'male_members' => $maleMembers,
            'female_members' => $femaleMembers,
            'above_18' => $this->faker->numberBetween(0, $familySize),
            'below_5' => $this->faker->numberBetween(0, $familySize),
            'financial_instrument' => $this->faker->randomElement(['bank', 'sacco', 'mobile money']),
            'available_energy_source' => $this->faker->randomElement(['grid', 'solar', 'generator']),
            'farm_size' => $this->faker->randomFloat(2, 1, 20),
            'land_under_use' => $this->faker->randomFloat(2, 0, 20),
            'land_ownership' => $this->faker->randomElement(['owned', 'leased', 'family']),
            'farming_type' => $this->faker->randomElement(['crop', 'livestock', 'mixed']),
            'crop_production' => $this->faker->randomElement(['maize', 'beans', 'coffee', 'sugarcane']),
            'animal_production' => $this->faker->randomElement(['dairy', 'beef', 'poultry', 'goats']),
            'is_farmer_insured' => $this->faker->boolean(),
            'support_needed' => $this->faker->optional()->sentence(),
        ];
    }

    protected function generateFarmerId(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return collect(range(1, 6))
            ->map(fn () => $characters[random_int(0, strlen($characters) - 1)])
            ->implode('');
    }
}
